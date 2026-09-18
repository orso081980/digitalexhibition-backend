<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Figuro_Admin_Page {

	const SLUG = 'figuro-media';

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
	}

	public static function add_menu() {
		add_media_page(
			__( 'Figuro Folders', 'figuro-media' ),
			__( 'Folders', 'figuro-media' ),
			'upload_files',
			self::SLUG,
			array( __CLASS__, 'render' )
		);
	}

	public static function enqueue( $hook ) {
		if ( 'media_page_' . self::SLUG !== $hook ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script( 'media-grid' );

		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'figuro-media', FIGURO_MEDIA_URL . 'assets/css/figuro-media.css', array(), FIGURO_MEDIA_VERSION );
		wp_enqueue_script( 'figuro-media', FIGURO_MEDIA_URL . 'assets/js/figuro-media.js', array( 'jquery', 'media-grid' ), FIGURO_MEDIA_VERSION, true );

		wp_localize_script(
			'figuro-media',
			'FiguroMedia',
			array(
				'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
				'nonce'         => wp_create_nonce( 'figuro_media_nonce' ),
				'maxUploadSize' => (int) wp_max_upload_size(),
				'i18n'          => array(
					'allFiles'        => __( 'All Files', 'figuro-media' ),
					'uncategorized'   => __( 'Uncategorized', 'figuro-media' ),
					'newFolder'       => __( 'New folder', 'figuro-media' ),
					'newFolderName'   => __( 'Folder name:', 'figuro-media' ),
					'rename'          => __( 'Rename', 'figuro-media' ),
					'renamePrompt'    => __( 'New name:', 'figuro-media' ),
					'delete'          => __( 'Delete', 'figuro-media' ),
					'deleteConfirm'   => __( 'Delete this folder? Files inside become Uncategorized. Subfolders move up one level.', 'figuro-media' ),
					'noItems'         => __( 'No files in this folder.', 'figuro-media' ),
					'loading'         => __( 'Loading…', 'figuro-media' ),
					'error'           => __( 'Something went wrong. Please try again.', 'figuro-media' ),
					'toggle'          => __( 'Toggle subfolders', 'figuro-media' ),
					'file'            => __( 'file', 'figuro-media' ),
					'files'           => __( 'files', 'figuro-media' ),
					'prevPage'        => __( 'Previous page', 'figuro-media' ),
					'nextPage'        => __( 'Next page', 'figuro-media' ),
					'uploading'       => __( 'Uploading…', 'figuro-media' ),
					'uploadDone'      => __( 'Uploaded', 'figuro-media' ),
					'fileTooBig'      => __( 'This file is larger than the server allows.', 'figuro-media' ),
					'bulkSelect'      => __( 'Bulk select', 'figuro-media' ),
					'cancel'          => __( 'Cancel', 'figuro-media' ),
					'deletePermanently' => __( 'Delete permanently', 'figuro-media' ),
					'itemSelected'    => __( 'item selected', 'figuro-media' ),
					'itemsSelected'   => __( 'items selected', 'figuro-media' ),
					'bulkDeleteConfirm' => __( "You are about to permanently delete these items from your site.\nThis action cannot be undone.", 'figuro-media' ),
				),
			)
		);
	}

	public static function render() {
		if ( ! current_user_can( 'upload_files' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'figuro-media' ) );
		}

		$status = get_option( Figuro_Migration::OPTION_KEY );
		?>
		<div class="wrap figuro-media-wrap">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Figuro Folders', 'figuro-media' ); ?></h1>
			<button type="button" class="page-title-action" id="figuro-add-media"><?php esc_html_e( 'Add Media File', 'figuro-media' ); ?></button>
			<input type="file" id="figuro-file-input" multiple hidden />
			<hr class="wp-header-end" />

			<div class="figuro-dropzone" id="figuro-dropzone">
				<div class="figuro-dropzone-inner"><?php esc_html_e( 'Drop files to upload', 'figuro-media' ); ?></div>
			</div>

			<div class="figuro-upload-log" id="figuro-upload-log"></div>

			<?php if ( $status && 'done' === $status['status'] ) : ?>
				<div class="figuro-migration-status">
					<span class="dashicons dashicons-yes-alt"></span>
					<p>
						<?php
						printf(
							/* translators: 1: folder count, 2: file count */
							esc_html__( 'Imported %1$d folders and %2$d files from FileBird.', 'figuro-media' ),
							(int) $status['folders_created'],
							(int) $status['attachments']
						);
						?>
					</p>
				</div>
			<?php endif; ?>

			<div id="figuro-media-app" class="figuro-media-app">
				<div class="figuro-media-sidebar">
					<div class="figuro-media-sidebar-head">
						<span class="figuro-sidebar-title"><?php esc_html_e( 'Folders', 'figuro-media' ); ?></span>
						<button type="button" class="figuro-icon-btn" id="figuro-add-root-folder" title="<?php esc_attr_e( 'New folder', 'figuro-media' ); ?>">
							<span class="dashicons dashicons-plus-alt2"></span>
						</button>
					</div>
					<ul class="figuro-tree figuro-tree-pinned" id="figuro-tree-pinned" aria-label="<?php esc_attr_e( 'Views', 'figuro-media' ); ?>"></ul>
					<div class="figuro-tree-scroll">
						<ul class="figuro-tree" id="figuro-tree" aria-label="<?php esc_attr_e( 'Folders', 'figuro-media' ); ?>"></ul>
					</div>
				</div>
				<div class="figuro-media-main">
					<div class="figuro-media-toolbar">
						<div class="figuro-toolbar-heading">
							<h2 id="figuro-current-folder-name"><?php esc_html_e( 'All Files', 'figuro-media' ); ?></h2>
							<span id="figuro-folder-count" class="figuro-folder-count"></span>
						</div>
						<div class="figuro-toolbar-actions">
							<div class="figuro-toolbar-filters">
								<div id="figuro-filter-type"></div>
								<div id="figuro-filter-date"></div>
							</div>
							<div class="figuro-bulk-bar" id="figuro-bulk-bar" hidden>
								<span class="figuro-bulk-count" id="figuro-bulk-count"></span>
								<button type="button" class="button button-link-delete" id="figuro-bulk-delete" disabled><?php esc_html_e( 'Delete permanently', 'figuro-media' ); ?></button>
								<button type="button" class="button" id="figuro-bulk-cancel"><?php esc_html_e( 'Cancel', 'figuro-media' ); ?></button>
							</div>
							<button type="button" class="button" id="figuro-bulk-toggle"><?php esc_html_e( 'Bulk select', 'figuro-media' ); ?></button>
							<div class="figuro-search-wrap">
								<span class="dashicons dashicons-search"></span>
								<input type="search" id="figuro-search" placeholder="<?php esc_attr_e( 'Search files…', 'figuro-media' ); ?>" />
							</div>
						</div>
					</div>
					<div class="figuro-grid" id="figuro-grid"></div>
					<div class="figuro-pagination" id="figuro-pagination"></div>
				</div>
			</div>
		</div>
		<?php
	}
}
