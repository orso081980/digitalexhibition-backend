<?php
/**
 * Adds the `pdf_file` sub-field (ACF File, media library picker) to the
 * `pdf_post` repeater, so editors can choose a PDF from the media library
 * right in each row of the PDF list.
 *
 * Why: acf_list_pdf_shortocde() (custom_dashboard/includes/shortcode.php) and
 * bin/migrate-pdf-file-field.php both expect this field. On a database where
 * it was never created, the repeater only has `shortcode_pdf` and `nome_pdf`,
 * the list renders nothing for rows that only have the old shortcode, and the
 * migration cannot write anything ACF is able to read back.
 *
 * Return format is "url" on purpose: the renderer hands the value straight to
 * [dflip source="..."]. ACF still stores the attachment ID internally.
 *
 * Safe to re-run: does nothing if `pdf_file` already exists under `pdf_post`.
 * `shortcode_pdf` is left alone so the migration can still read it.
 *
 * Usage:
 *   wp eval-file bin/add-pdf-file-field.php
 */

if (!defined('WP_CLI') || !WP_CLI) {
    echo "Run this via: wp eval-file bin/add-pdf-file-field.php\n";
    exit(1);
}

if (!function_exists('acf_get_field') || !function_exists('acf_update_field')) {
    WP_CLI::error('ACF is not active.');
}

$repeater = acf_get_field('pdf_post');
if (!$repeater) {
    WP_CLI::error('Could not find the "pdf_post" repeater field.');
}

foreach (acf_get_fields($repeater) ?: [] as $sub) {
    if ($sub['name'] === 'pdf_file') {
        WP_CLI::success("pdf_file already exists under pdf_post ({$sub['key']}). Nothing to do.");
        return;
    }
}

$field = acf_update_field([
    'key'           => 'field_' . substr(md5('pdf_post/pdf_file'), 0, 13),
    'label'         => 'PDF file',
    'name'          => 'pdf_file',
    'type'          => 'file',
    'parent'        => $repeater['ID'],
    'instructions'  => 'Choose or upload the PDF from the media library.',
    'required'      => 0,
    'return_format' => 'url',
    'library'       => 'all',
    'min_size'      => '',
    'max_size'      => '',
    'mime_types'    => 'pdf',
    'menu_order'    => 2,
]);

WP_CLI::success("Created pdf_file ({$field['key']}) under pdf_post.");
