<?php
/**
 * One-off backfill: populate the `pdf_file` sub-field on every `pdf_post`
 * repeater row that only has a legacy `[dflip id="X"]` shortcode, by
 * resolving that shortcode's dflip catalog entry back to the actual media
 * library attachment it points at.
 *
 * Why: the `pdf_file` field was added to let editors upload a PDF straight to
 * the repeater row instead of always creating a `dflip` catalog post first
 * (see custom_dashboard/includes/shortcode.php, acf_list_pdf_shortocde()).
 * Existing rows created before that only have `shortcode_pdf` filled in;
 * this backfills `pdf_file` for those so they render the same way and can
 * eventually have their shortcode removed without losing the PDF.
 *
 * IMPORTANT: `pdf_file` is an ACF File field with return_format "url" — ACF
 * still stores an attachment POST ID internally and only formats it to a URL
 * on read. Writing a raw URL string into it silently breaks the field (it
 * reads back empty). This script always resolves down to the attachment ID.
 * Media here is offloaded to R2, so the delivered URL doesn't match this
 * site's own upload base and `attachment_url_to_postid()` alone can't find
 * it — this falls back to matching `_wp_attached_file` by relative path.
 *
 * Safe to re-run: rows that already have `pdf_file` set are left untouched,
 * and rows whose shortcode doesn't resolve to a usable attachment are
 * reported and skipped, never guessed at.
 *
 * Usage (dry run — reports what it would change, writes nothing):
 *   wp eval-file bin/migrate-pdf-file-field.php
 *
 * Usage (apply for real):
 *   wp eval-file bin/migrate-pdf-file-field.php apply
 */

if (!defined('WP_CLI') || !WP_CLI) {
    echo "Run this via: wp eval-file bin/migrate-pdf-file-field.php [apply]\n";
    exit(1);
}

$apply = in_array('apply', $args ?? [], true);

echo $apply ? "Running in APPLY mode — will write changes.\n" : "Running in DRY-RUN mode — no changes will be written. Pass \"apply\" as an argument to write.\n";
echo str_repeat('-', 70) . "\n";

/**
 * Resolve a (possibly offloaded/CDN) attachment URL back to its attachment
 * post ID. Tries the normal WP path first, then falls back to matching the
 * relative upload path stored in `_wp_attached_file`, which stays correct
 * even when the delivery domain (R2) differs from this site's upload base.
 */
function cd_resolve_attachment_id($url)
{
    $id = attachment_url_to_postid($url);
    if ($id) {
        return $id;
    }

    $path = wp_parse_url($url, PHP_URL_PATH);
    if (!$path) {
        return 0;
    }

    $marker = '/uploads/';
    $pos = strpos($path, $marker);
    if ($pos === false) {
        return 0;
    }
    $relative = ltrim(substr($path, $pos + strlen($marker)), '/');

    global $wpdb;
    $id = $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value = %s LIMIT 1",
        $relative
    ));

    return $id ? (int) $id : 0;
}

$post_types = ['post', 'courses', 'seminars', 'archiprix'];

$query = new WP_Query([
    'post_type'      => $post_types,
    'post_status'    => 'any',
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'no_found_rows'  => true,
]);

$updated_rows = 0;
$skipped_rows = 0;
$posts_touched = 0;

foreach ($query->posts as $post_id) {
    if (!have_rows('pdf_post', $post_id)) {
        continue;
    }

    $row_index = 0;
    $post_touched_this_post = false;

    while (have_rows('pdf_post', $post_id)) {
        the_row();
        $row_index++;

        $pdf_file = get_sub_field('pdf_file');
        if ($pdf_file) {
            continue; // already has a real upload — never overwrite
        }

        $shortcode = get_sub_field('shortcode_pdf');
        if (!$shortcode) {
            continue; // nothing to migrate from
        }

        if (!preg_match('/\[dflip[^\]]*\bid=["\']?(\d+)["\']?/i', $shortcode, $m)) {
            echo "SKIP  post {$post_id} row {$row_index}: shortcode has no numeric id ({$shortcode})\n";
            $skipped_rows++;
            continue;
        }

        $dflip_id = (int) $m[1];
        $dflip_post = get_post($dflip_id);

        if (!$dflip_post) {
            echo "SKIP  post {$post_id} row {$row_index}: dflip id {$dflip_id} does not exist\n";
            $skipped_rows++;
            continue;
        }

        $attachment_id = 0;

        if ($dflip_post->post_mime_type === 'application/pdf') {
            // The shortcode pointed straight at a PDF attachment, not a dflip
            // catalog post — dFlip supports this too (DFlip_ShortCode::book()).
            $attachment_id = $dflip_id;
        } elseif ($dflip_post->post_type === 'dflip') {
            $dflip_meta = get_post_meta($dflip_id, '_dflip_data', true);
            $source_type = $dflip_meta['source_type'] ?? '';
            if ($source_type === 'pdf' && !empty($dflip_meta['pdf_source'])) {
                $attachment_id = cd_resolve_attachment_id($dflip_meta['pdf_source']);
                if (!$attachment_id) {
                    echo "SKIP  post {$post_id} row {$row_index}: dflip id {$dflip_id}'s pdf_source ({$dflip_meta['pdf_source']}) isn't a media library attachment on this site — needs manual upload\n";
                    $skipped_rows++;
                    continue;
                }
            } else {
                echo "SKIP  post {$post_id} row {$row_index}: dflip id {$dflip_id} has source_type=\"{$source_type}\" (not a single PDF, e.g. an image-based flipbook) — needs manual handling\n";
                $skipped_rows++;
                continue;
            }
        } else {
            echo "SKIP  post {$post_id} row {$row_index}: dflip id {$dflip_id} is a \"{$dflip_post->post_type}\" post, not dflip/attachment\n";
            $skipped_rows++;
            continue;
        }

        echo ($apply ? "SET   " : "WOULD SET ") . "post {$post_id} (\"" . get_the_title($post_id) . "\") row {$row_index}: pdf_file = attachment #{$attachment_id} (" . wp_get_attachment_url($attachment_id) . ")\n";

        if ($apply) {
            update_sub_field(['pdf_post', $row_index, 'pdf_file'], $attachment_id, $post_id);
        }

        $updated_rows++;
        $post_touched_this_post = true;
    }

    if ($post_touched_this_post) {
        $posts_touched++;
    }
}

echo str_repeat('-', 70) . "\n";
echo "Posts touched: {$posts_touched}\n";
echo ($apply ? "Rows updated: " : "Rows that would be updated: ") . "{$updated_rows}\n";
echo "Rows skipped (need manual review): {$skipped_rows}\n";

if (!$apply && $updated_rows > 0) {
    echo "\nRe-run with \"apply\" as an argument to write these changes.\n";
}
