<?php
/**
 * Adds the `video_url` sub-field (ACF File, media library picker) to the
 * `list_video` repeater, so editors can choose a video from the media library
 * right in each row of the video gallery instead of pasting an "Embed Video"
 * address.
 *
 * Why: a pasted address of an uploaded video is a hard-coded URL on whatever
 * host the content was written on. Once media is offloaded to R2 that host no
 * longer has the file, so the video breaks (the media offloader can only
 * rewrite attachments it knows by ID). A file field stores the attachment ID,
 * so the offloader serves the video from R2 like every other upload.
 * cd_video_gallery_shortcode() (custom_dashboard/includes/shortcode.php) and
 * bin/migrate-video-file-field.php both expect this field.
 *
 * Return format is "url" on purpose: the renderer hands the value straight to
 * a <video> tag. ACF still stores the attachment ID internally.
 *
 * Safe to re-run: does nothing if `video_url` already exists under `list_video`.
 * `embed_video` is left alone so the migration can still read it, and YouTube /
 * Vimeo rows keep working.
 *
 * Usage:
 *   wp eval-file bin/add-video-file-field.php
 */

if (!defined('WP_CLI') || !WP_CLI) {
    echo "Run this via: wp eval-file bin/add-video-file-field.php\n";
    exit(1);
}

if (!function_exists('acf_get_field') || !function_exists('acf_update_field')) {
    WP_CLI::error('ACF is not active.');
}

$repeater = acf_get_field('list_video');
if (!$repeater) {
    WP_CLI::error('Could not find the "list_video" repeater field.');
}

foreach (acf_get_fields($repeater) ?: [] as $sub) {
    if ($sub['name'] === 'video_url') {
        WP_CLI::success("video_url already exists under list_video ({$sub['key']}). Nothing to do.");
        return;
    }
}

$field = acf_update_field([
    'key'           => 'field_' . substr(md5('list_video/video_url'), 0, 13),
    'label'         => 'Video url',
    'name'          => 'video_url',
    'type'          => 'file',
    'parent'        => $repeater['ID'],
    'instructions'  => 'Choose or upload the video from the media library.',
    'required'      => 0,
    'return_format' => 'url',
    'library'       => 'all',
    'min_size'      => '',
    'max_size'      => '',
    'mime_types'    => 'mp4, webm, ogv',
    'menu_order'    => 0,
]);

WP_CLI::success("Created video_url ({$field['key']}) under list_video.");
