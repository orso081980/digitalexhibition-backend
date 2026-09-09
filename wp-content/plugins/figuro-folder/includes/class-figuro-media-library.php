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

		add_filter( 'manage_media_columns', array( __CLASS__, 'add_column' ) );
		add_action( 'manage_media_custom_column', array( __CLASS__, 'render_column' ), 10, 2 );
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
