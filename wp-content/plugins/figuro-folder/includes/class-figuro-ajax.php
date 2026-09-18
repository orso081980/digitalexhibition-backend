<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Figuro_Ajax {

	public static function init() {
		$actions = array(
			'figuro_bootstrap'         => 'bootstrap',
			'figuro_get_tree'          => 'get_tree',
			'figuro_create_folder'     => 'create_folder',
			'figuro_rename_folder'     => 'rename_folder',
			'figuro_move_folder'       => 'move_folder',
			'figuro_delete_folder'     => 'delete_folder',
			'figuro_get_attachments'   => 'get_attachments',
			'figuro_move_attachments'  => 'move_attachments',
			'figuro_delete_attachments' => 'delete_attachments',
			'figuro_upload_attachment' => 'upload_attachment',
			'figuro_rerun_migration'   => 'rerun_migration',
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
		wp_send_json_success( self::tree_payload() );
	}

	/**
	 * Folder tree + counts, as a plain array (not a JSON response) so it can
	 * be reused both by get_tree() and embedded in other endpoints' payloads
	 * to save a follow-up round trip.
	 */
	private static function tree_payload() {
		return array(
			'tree'   => Figuro_Taxonomy::get_tree(),
			'counts' => Figuro_Taxonomy::get_totals(),
		);
	}

	/**
	 * The initial "Folders" screen needs the tree and the default "All Files"
	 * grid before it can render anything useful. Fetching both in one request
	 * avoids paying admin-ajax.php's full WordPress bootstrap twice just to
	 * paint the first screen.
	 */
	public static function bootstrap() {
		self::check();

		$query = Figuro_Taxonomy::get_attachments( '', 1, 60, '', array() );

		wp_send_json_success(
			array_merge(
				self::tree_payload(),
				array( 'attachments' => self::attachments_payload( $query, 1 ) )
			)
		);
	}

	public static function create_folder() {
		self::check();

		$name   = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$parent = isset( $_POST['parent'] ) ? absint( $_POST['parent'] ) : 0;

		$result = Figuro_Taxonomy::create_folder( $name, $parent );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( array_merge( array( 'id' => (int) $result['term_id'] ), self::tree_payload() ) );
	}

	public static function rename_folder() {
		self::check();

		$id   = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';

		$result = Figuro_Taxonomy::rename_folder( $id, $name );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( self::tree_payload() );
	}

	public static function move_folder() {
		self::check();

		$id     = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		$parent = isset( $_POST['parent'] ) ? absint( $_POST['parent'] ) : 0;

		$result = Figuro_Taxonomy::move_folder( $id, $parent );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( self::tree_payload() );
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

		wp_send_json_success( self::tree_payload() );
	}

	public static function get_attachments() {
		self::check();

		$folder = isset( $_POST['folder'] ) ? sanitize_text_field( wp_unslash( $_POST['folder'] ) ) : '';
		$paged  = isset( $_POST['paged'] ) ? max( 1, absint( $_POST['paged'] ) ) : 1;
		$search = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';

		$folder_arg = ( 'uncategorized' === $folder ) ? 'uncategorized' : ( '' === $folder ? '' : absint( $folder ) );

		// Mirrors the core Media Library's "All media items" / "All dates" filters.
		$filters = array(
			'mime_type'   => isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '',
			'uploaded_to' => isset( $_POST['uploadedTo'] ) && '' !== $_POST['uploadedTo'] ? absint( $_POST['uploadedTo'] ) : null,
			'author'      => isset( $_POST['author'] ) ? absint( $_POST['author'] ) : 0,
			'year'        => isset( $_POST['year'] ) ? absint( $_POST['year'] ) : 0,
			'monthnum'    => isset( $_POST['monthnum'] ) ? absint( $_POST['monthnum'] ) : 0,
		);

		$query = Figuro_Taxonomy::get_attachments( $folder_arg, $paged, 60, $search, $filters );

		wp_send_json_success( self::attachments_payload( $query, $paged ) );
	}

	/**
	 * @param WP_Query $query
	 * @param int      $paged
	 * @return array{items:array,page:int,totalPages:int,total:int}
	 */
	private static function attachments_payload( WP_Query $query, $paged ) {
		$items = array();
		foreach ( $query->posts as $post ) {
			$thumb   = wp_get_attachment_image_src( $post->ID, 'thumbnail' );
			$items[] = array(
				'id'    => $post->ID,
				'title' => get_the_title( $post ),
				'thumb' => $thumb ? $thumb[0] : wp_mime_type_icon( $post->ID ),
			);
		}

		return array(
			'items'      => $items,
			'page'       => $paged,
			'totalPages' => (int) $query->max_num_pages,
			'total'      => (int) $query->found_posts,
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

		wp_send_json_success( array_merge( array( 'moved' => $moved ), self::tree_payload() ) );
	}

	/**
	 * Permanently deletes one or more attachments (bulk select action).
	 */
	public static function delete_attachments() {
		self::check();

		$ids = isset( $_POST['ids'] ) ? array_map( 'absint', (array) $_POST['ids'] ) : array();

		if ( empty( $ids ) ) {
			wp_send_json_error( array( 'message' => __( 'No files selected.', 'figuro-media' ) ) );
		}

		require_once ABSPATH . 'wp-admin/includes/image.php';

		$deleted = 0;
		foreach ( $ids as $id ) {
			if ( ! current_user_can( 'delete_post', $id ) ) {
				continue;
			}
			if ( wp_delete_attachment( $id, true ) ) {
				$deleted++;
			}
		}

		if ( ! $deleted ) {
			wp_send_json_error( array( 'message' => __( 'Could not delete the selected files.', 'figuro-media' ) ) );
		}

		wp_send_json_success( array_merge( array( 'deleted' => $deleted ), self::tree_payload() ) );
	}

	/**
	 * Uploads a single file (via the "Add Media File" button or dropping it
	 * onto the page) straight into the currently browsed folder, using the
	 * same core pipeline as a normal Media Library upload.
	 */
	public static function upload_attachment() {
		self::check();

		if ( empty( $_FILES['file'] ) ) {
			wp_send_json_error( array( 'message' => __( 'No file received.', 'figuro-media' ) ) );
		}

		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$attachment_id = media_handle_upload( 'file', 0 );

		if ( is_wp_error( $attachment_id ) ) {
			wp_send_json_error( array( 'message' => $attachment_id->get_error_message() ) );
		}

		$folder = isset( $_POST['folder'] ) ? sanitize_text_field( wp_unslash( $_POST['folder'] ) ) : '';
		if ( '' !== $folder && 'uncategorized' !== $folder ) {
			Figuro_Taxonomy::set_attachment_folder( $attachment_id, absint( $folder ) );
		}

		$thumb = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );

		wp_send_json_success(
			array_merge(
				array(
					'id'    => $attachment_id,
					'title' => get_the_title( $attachment_id ),
					'thumb' => $thumb ? $thumb[0] : wp_mime_type_icon( $attachment_id ),
				),
				self::tree_payload()
			)
		);
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
