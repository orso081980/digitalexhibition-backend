<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Light integration with the built-in Media Library list view: a folder
 * filter dropdown, a "Folder" column, and a bulk action to move files.
 * The full folder-tree experience lives on its own Figuro Folders page.
 */
class Figuro_Media_Library {

	const QUERY_VAR   = 'figuro_folder';
	const BULK_PREFIX = 'figuro_move_to_';

	public static function init() {
		add_action( 'restrict_manage_posts', array( __CLASS__, 'folder_dropdown' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'filter_by_folder' ) );

		add_filter( 'bulk_actions-upload', array( __CLASS__, 'add_bulk_actions' ) );
		add_filter( 'handle_bulk_actions-upload', array( __CLASS__, 'handle_bulk_action' ), 10, 3 );
		add_action( 'admin_notices', array( __CLASS__, 'bulk_action_notice' ) );

		// Folder sidebar inside the media popup (ACF file fields, "Add Media", …).
		add_action( 'wp_enqueue_media', array( __CLASS__, 'enqueue_picker' ) );
		add_filter( 'ajax_query_attachments_args', array( __CLASS__, 'filter_modal_query' ) );
		add_action( 'add_attachment', array( __CLASS__, 'assign_uploaded_folder' ) );

		add_filter( 'manage_media_columns', array( __CLASS__, 'add_column' ) );
		add_action( 'manage_media_custom_column', array( __CLASS__, 'render_column' ), 10, 2 );
	}

	/**
	 * Loads the popup's folder sidebar (see assets/js/figuro-picker.js).
	 * Skipped on the Figuro Folders page, which has its own folder tree.
	 */
	public static function enqueue_picker() {
		if ( ! current_user_can( 'upload_files' ) ) {
			return;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( $screen && 'media_page_' . Figuro_Admin_Page::SLUG === $screen->id ) {
			return;
		}

		wp_enqueue_style( 'figuro-media-picker', FIGURO_MEDIA_URL . 'assets/css/figuro-picker.css', array( 'dashicons' ), FIGURO_MEDIA_VERSION );
		wp_enqueue_script( 'figuro-media-picker', FIGURO_MEDIA_URL . 'assets/js/figuro-picker.js', array( 'jquery', 'media-views' ), FIGURO_MEDIA_VERSION, true );
		wp_localize_script(
			'figuro-media-picker',
			'FiguroPicker',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'figuro_media_nonce' ),
				'tree'    => Figuro_Taxonomy::get_tree(),
				'totals'  => Figuro_Taxonomy::get_totals(),
				'i18n'    => array(
					'title'         => __( 'Folders', 'figuro-media' ),
					'newFolder'     => __( 'New Folder', 'figuro-media' ),
					'newFolderName' => __( 'Folder name:', 'figuro-media' ),
					'rename'        => __( 'Rename', 'figuro-media' ),
					'renamePrompt'  => __( 'New name:', 'figuro-media' ),
					'delete'        => __( 'Delete', 'figuro-media' ),
					'deleteConfirm' => __( 'Delete this folder? Files inside become Uncategorized. Subfolders move up one level.', 'figuro-media' ),
					'allFiles'      => __( 'All Files', 'figuro-media' ),
					'uncategorized' => __( 'Uncategorized', 'figuro-media' ),
					'search'        => __( 'Enter folder name…', 'figuro-media' ),
					'toggle'        => __( 'Toggle subfolders', 'figuro-media' ),
					'error'         => __( 'Something went wrong. Please try again.', 'figuro-media' ),
				),
			)
		);
	}

	/**
	 * Applies the popup's folder choice to its library query. Core only passes
	 * a fixed list of `query` keys through to WP_Query, so the custom
	 * `figuro_folder` one is read straight from the request.
	 */
	public static function filter_modal_query( $args ) {
		if ( empty( $_REQUEST['query'] ) || ! is_array( $_REQUEST['query'] ) || ! isset( $_REQUEST['query'][ self::QUERY_VAR ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $args;
		}

		$value = sanitize_text_field( wp_unslash( $_REQUEST['query'][ self::QUERY_VAR ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( '' === $value ) {
			return $args;
		}

		$tax_query = isset( $args['tax_query'] ) && is_array( $args['tax_query'] ) ? $args['tax_query'] : array();

		if ( 'uncategorized' === $value ) {
			$tax_query[] = array(
				'taxonomy' => FIGURO_MEDIA_TAXONOMY,
				'operator' => 'NOT EXISTS',
			);
		} else {
			$tax_query[] = array(
				'taxonomy' => FIGURO_MEDIA_TAXONOMY,
				'field'    => 'term_id',
				'terms'    => absint( $value ),
			);
		}

		$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query

		return $args;
	}

	/**
	 * A file uploaded from the popup while a folder is selected lands in that
	 * folder (the sidebar sends the choice along as `figuro_folder`).
	 */
	public static function assign_uploaded_folder( $attachment_id ) {
		if ( empty( $_REQUEST[ self::QUERY_VAR ] ) || ! current_user_can( 'upload_files' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$folder_id = absint( wp_unslash( $_REQUEST[ self::QUERY_VAR ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $folder_id && term_exists( $folder_id, FIGURO_MEDIA_TAXONOMY ) ) {
			Figuro_Taxonomy::set_attachment_folder( $attachment_id, $folder_id );
		}
	}

	public static function folder_dropdown( $post_type ) {
		if ( 'attachment' !== $post_type || ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen || 'upload' !== $screen->id ) {
			return;
		}

		$tree    = Figuro_Taxonomy::get_tree();
		$current = isset( $_GET[ self::QUERY_VAR ] ) ? sanitize_text_field( wp_unslash( $_GET[ self::QUERY_VAR ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		echo '<select name="' . esc_attr( self::QUERY_VAR ) . '" id="figuro-folder-filter">';
		echo '<option value="">' . esc_html__( 'All folders', 'figuro-media' ) . '</option>';
		echo '<option value="uncategorized"' . selected( $current, 'uncategorized', false ) . '>' . esc_html__( 'Uncategorized', 'figuro-media' ) . '</option>';
		self::render_options( $tree, $current, 0 );
		echo '</select>';
	}

	private static function render_options( $nodes, $current, $depth ) {
		foreach ( $nodes as $node ) {
			printf(
				'<option value="%1$d"%2$s>%3$s%4$s</option>',
				(int) $node['id'],
				selected( $current, (string) $node['id'], false ),
				str_repeat( '&nbsp;&nbsp;&nbsp;', $depth ),
				esc_html( $node['name'] )
			);
			if ( ! empty( $node['children'] ) ) {
				self::render_options( $node['children'], $current, $depth + 1 );
			}
		}
	}

	public static function filter_by_folder( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}
		if ( 'attachment' !== $query->get( 'post_type' ) ) {
			return;
		}
		if ( empty( $_GET[ self::QUERY_VAR ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$value = sanitize_text_field( wp_unslash( $_GET[ self::QUERY_VAR ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( 'uncategorized' === $value ) {
			$query->set(
				'tax_query', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					array(
						'taxonomy' => FIGURO_MEDIA_TAXONOMY,
						'operator' => 'NOT EXISTS',
					),
				)
			);
			return;
		}

		$query->set(
			'tax_query', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				array(
					'taxonomy' => FIGURO_MEDIA_TAXONOMY,
					'field'    => 'term_id',
					'terms'    => absint( $value ),
				),
			)
		);
	}

	public static function add_bulk_actions( $actions ) {
		$flat = array();
		self::flatten_for_bulk( Figuro_Taxonomy::get_tree(), 0, $flat );

		$actions[ self::BULK_PREFIX . '0' ] = __( 'Move to: Uncategorized', 'figuro-media' );

		foreach ( $flat as $node ) {
			$label                                     = str_repeat( '— ', $node['depth'] ) . $node['name'];
			$actions[ self::BULK_PREFIX . $node['id'] ] = sprintf(
				/* translators: %s: folder name */
				__( 'Move to: %s', 'figuro-media' ),
				$label
			);
		}

		return $actions;
	}

	private static function flatten_for_bulk( $nodes, $depth, array &$out ) {
		foreach ( $nodes as $node ) {
			$out[] = array(
				'id'    => $node['id'],
				'name'  => $node['name'],
				'depth' => $depth,
			);
			if ( ! empty( $node['children'] ) ) {
				self::flatten_for_bulk( $node['children'], $depth + 1, $out );
			}
		}
	}

	public static function handle_bulk_action( $redirect_to, $action, $post_ids ) {
		if ( 0 !== strpos( $action, self::BULK_PREFIX ) ) {
			return $redirect_to;
		}

		$folder_id = (int) substr( $action, strlen( self::BULK_PREFIX ) );
		$moved     = 0;

		foreach ( $post_ids as $post_id ) {
			$result = Figuro_Taxonomy::set_attachment_folder( $post_id, $folder_id );
			if ( ! is_wp_error( $result ) ) {
				$moved++;
			}
		}

		return add_query_arg( 'figuro_moved', $moved, $redirect_to );
	}

	public static function bulk_action_notice() {
		if ( empty( $_GET['figuro_moved'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$count = absint( $_GET['figuro_moved'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		printf(
			'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
			esc_html(
				sprintf(
					/* translators: %d: number of files moved */
					_n( '%d file moved to the selected folder.', '%d files moved to the selected folder.', $count, 'figuro-media' ),
					$count
				)
			)
		);
	}

	public static function add_column( $columns ) {
		$columns['figuro_folder'] = __( 'Folder', 'figuro-media' );
		return $columns;
	}

	public static function render_column( $column_name, $post_id ) {
		if ( 'figuro_folder' !== $column_name ) {
			return;
		}

		$terms = get_the_terms( $post_id, FIGURO_MEDIA_TAXONOMY );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			echo '<span class="figuro-uncat">' . esc_html__( 'Uncategorized', 'figuro-media' ) . '</span>';
			return;
		}

		echo esc_html( $terms[0]->name );
	}
}
