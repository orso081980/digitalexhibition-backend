<?php
/**
 * One-off backfill: populate the `video_url` sub-field on every `list_video`
 * repeater row whose "Embed Video" holds the address of a video uploaded to
 * the media library, by resolving that address back to the actual attachment.
 *
 * Why: see bin/add-video-file-field.php. A pasted upload address breaks once
 * the file lives only on R2; the file field stores the attachment ID instead,
 * which the media offloader can serve from R2. This is the same step
 * bin/migrate-pdf-file-field.php did for the PDF list.
 *
 * IMPORTANT: `video_url` is an ACF File field with return_format "url" — ACF
 * still stores an attachment POST ID internally and only formats it to a URL
 * on read. Writing a raw URL string into it silently breaks the field (it
 * reads back empty). This script always resolves down to the attachment ID.
 * Media here is offloaded to R2, so the address in the old field may not match
 * this site's own upload base and `attachment_url_to_postid()` alone can't
 * find it — this falls back to matching `_wp_attached_file` by relative path,
 * which works whichever host (this site, staging, live or R2) the address names.
 *
 * Safe to re-run: rows that already have `video_url` set are left untouched.
 * Rows whose "Embed Video" is not an uploaded video (a YouTube or Vimeo link)
 * or does not resolve to a video attachment are reported and skipped, never
 * guessed at — they keep rendering through the embed.
 *
 * Usage (dry run — reports what it would change, writes nothing):
 *   wp eval-file bin/migrate-video-file-field.php
 *
 * Usage (apply for real):
 *   wp eval-file bin/migrate-video-file-field.php apply
 */

if (!defined('WP_CLI') || !WP_CLI) {
    echo "Run this via: wp eval-file bin/migrate-video-file-field.php [apply]\n";
    exit(1);
}

if (!function_exists('acf_get_field')) {
    WP_CLI::error('ACF is not active.');
}

$repeater = acf_get_field('list_video');
$has_new_field = false;
foreach ($repeater ? (acf_get_fields($repeater) ?: []) : [] as $sub) {
    if ($sub['name'] === 'video_url') {
        $has_new_field = true;
    }
}
if (!$has_new_field) {
    WP_CLI::error('The "video_url" field does not exist under "list_video". Run: wp eval-file bin/add-video-file-field.php');
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
if (!function_exists('cd_resolve_attachment_id')) {
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
        $relative = ltrim(rawurldecode(substr($path, $pos + strlen($marker))), '/');

        global $wpdb;
        $id = $wpdb->get_var($wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value = %s LIMIT 1",
            $relative
        ));

        return $id ? (int) $id : 0;
    }
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
    if (!have_rows('list_video', $post_id)) {
        continue;
    }

    $row_index = 0;
    $post_touched_this_post = false;

    while (have_rows('list_video', $post_id)) {
        the_row();
        $row_index++;

        $video_file = get_sub_field('video_url');
        if ($video_file) {
            continue; // already has a real upload — never overwrite
        }

        // format_value = false: the raw address, not the embed HTML ACF builds from it.
        $embed = get_sub_field('embed_video', false);
        if (!$embed || !is_string($embed)) {
            continue; // nothing to migrate from
        }

        $embed = trim($embed);
        if (!preg_match('#/uploads/#', $embed) || !preg_match('/\.(mp4|m4v|webm|ogv|mov)(\?|#|$)/i', $embed)) {
            echo "SKIP  post {$post_id} row {$row_index}: \"Embed Video\" is not an uploaded video file ({$embed}) — keeps rendering as an embed\n";
            $skipped_rows++;
            continue;
        }

        $attachment_id = cd_resolve_attachment_id($embed);
        if (!$attachment_id) {
            echo "SKIP  post {$post_id} row {$row_index}: {$embed} isn't a media library attachment on this site — needs manual upload\n";
            $skipped_rows++;
            continue;
        }

        $mime = (string) get_post_mime_type($attachment_id);
        if (strpos($mime, 'video/') !== 0) {
            echo "SKIP  post {$post_id} row {$row_index}: attachment #{$attachment_id} is \"{$mime}\", not a video\n";
            $skipped_rows++;
            continue;
        }

        echo ($apply ? "SET   " : "WOULD SET ") . "post {$post_id} (\"" . get_the_title($post_id) . "\") row {$row_index}: video_url = attachment #{$attachment_id} (" . wp_get_attachment_url($attachment_id) . ")\n";

        if ($apply) {
            update_sub_field(['list_video', $row_index, 'video_url'], $attachment_id, $post_id);
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
