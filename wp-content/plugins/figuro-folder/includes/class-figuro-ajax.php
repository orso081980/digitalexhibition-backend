<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Figuro_Ajax {

	public static function init() {
		$actions = array(
			'figuro_get_tree'         => 'get_tree',
			'figuro_create_folder'    => 'create_folder',
			'figuro_rename_folder'    => 'rename_folder',
			'figuro_move_folder'      => 'move_folder',
			'figuro_delete_folder'    => 'delete_folder',
			'figuro_get_attachments'  => 'get_attachments',
			'figuro_move_attachments' => 'move_attachments',
			'figuro_rerun_migration'  => 'rerun_migration',
		);

		foreach ( $actions as $action => $method ) {
			add_action( 'wp_ajax_' . $action, array( __CLASS__, $method ) );
		}
	}

	private static function check() {
		if ( ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'figuro-media' ) ), 403 );
		}
		check_ajax_referer( 'figuro_media_nonce', 'nonce' );
	}

	public static function get_tree() {
		self::check();
		wp_send_json_success( array( 'tree' => Figuro_Taxonomy::get_tree() ) );
	}

	public static function create_folder() {
		self::check();

		$name   = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$parent = isset( $_POST['parent'] ) ? absint( $_POST['parent'] ) : 0;

		$result = Figuro_Taxonomy::create_folder( $name, $parent );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( array( 'id' => (int) $result['term_id'] ) );
	}

	public static function rename_folder() {
		self::check();

		$id   = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';

		$result = Figuro_Taxonomy::rename_folder( $id, $name );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success();
	}

	public static function move_folder() {
		self::check();

		$id     = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		$parent = isset( $_POST['parent'] ) ? absint( $_POST['parent'] ) : 0;

		$result = Figuro_Taxonomy::move_folder( $id, $parent );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success();
	}

	public static function delete_folder() {
		self::check();

		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

		$result = Figuro_Taxonomy::delete_folder( $id );

		if ( is_wp_error( $result ) || false === $result ) {
			wp_send_json_error(
				array(
					'message' => is_wp_error( $result ) ? $result->get_error_message() : __( 'Could not delete folder.', 'figuro-media' ),
				)
			);
		}

		wp_send_json_success();
	}

	public static function get_attachments() {
		self::check();

		$folder = isset( $_POST['folder'] ) ? sanitize_text_field( wp_unslash( $_POST['folder'] ) ) : '';
		$paged  = isset( $_POST['paged'] ) ? max( 1, absint( $_POST['paged'] ) ) : 1;
		$search = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';

		$folder_arg = ( 'uncategorized' === $folder ) ? 'uncategorized' : ( '' === $folder ? '' : absint( $folder ) );

		$query = Figuro_Taxonomy::get_attachments( $folder_arg, $paged, 60, $search );

		$items = array();
		foreach ( $query->posts as $post ) {
			$thumb     = wp_get_attachment_image_src( $post->ID, 'thumbnail' );
			$items[] = array(
				'id'    => $post->ID,
				'title' => get_the_title( $post ),
				'thumb' => $thumb ? $thumb[0] : wp_mime_type_icon( $post->ID ),
			);
		}

		wp_send_json_success(
			array(
				'items'      => $items,
				'page'       => $paged,
				'totalPages' => (int) $query->max_num_pages,
				'total'      => (int) $query->found_posts,
			)
		);
	}

	public static function move_attachments() {
		self::check();

		$ids       = isset( $_POST['ids'] ) ? array_map( 'absint', (array) $_POST['ids'] ) : array();
		$folder    = isset( $_POST['folder'] ) ? sanitize_text_field( wp_unslash( $_POST['folder'] ) ) : '';
		$folder_id = ( 'uncategorized' === $folder || '' === $folder ) ? 0 : absint( $folder );

		if ( empty( $ids ) ) {
			wp_send_json_error( array( 'message' => __( 'No files selected.', 'figuro-media' ) ) );
		}

		$moved = 0;
		foreach ( $ids as $id ) {
			$result = Figuro_Taxonomy::set_attachment_folder( $id, $folder_id );
			if ( ! is_wp_error( $result ) ) {
				$moved++;
			}
		}

		wp_send_json_success( array( 'moved' => $moved ) );
	}

	public static function rerun_migration() {
		self::check();

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'figuro-media' ) ), 403 );
		}

		Figuro_Migration::run();

		wp_send_json_success( array( 'status' => get_option( Figuro_Migration::OPTION_KEY ) ) );
	}
}
