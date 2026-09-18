<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Folders are stored as a hierarchical taxonomy on the attachment post type.
 * This reuses WordPress's own terms/relationships tables instead of adding
 * new ones, and gives us hierarchy, counts and querying for free.
 */
class Figuro_Taxonomy {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );

		// Adds a "Folder" <select> to the core Attachment Details view (the
		// same modal figuro-media.js opens), so the folder can be changed
		// from there like any other attachment field.
		add_filter( 'attachment_fields_to_edit', array( __CLASS__, 'add_folder_field' ), 10, 2 );
		add_filter( 'attachment_fields_to_save', array( __CLASS__, 'save_folder_field' ), 10, 2 );

		// Core's "All media items" / "Documents" filter already matches PDFs
		// (they're bundled together with Word docs, RTF, etc.), but there's no
		// way to filter for just PDFs on their own. Add a dedicated entry —
		// this feeds both our own type filter and the core Media Library's.
		add_filter( 'post_mime_types', array( __CLASS__, 'add_pdf_mime_type' ) );
	}

	/**
	 * @param array $post_mime_types
	 * @return array
	 */
	public static function add_pdf_mime_type( $post_mime_types ) {
		if ( ! isset( $post_mime_types['application/pdf'] ) ) {
			$post_mime_types['application/pdf'] = array(
				__( 'PDFs', 'figuro-media' ),
				__( 'Manage PDFs', 'figuro-media' ),
				_n_noop(
					'PDF <span class="count">(%s)</span>',
					'PDFs <span class="count">(%s)</span>',
					'figuro-media'
				),
			);
		}

		return $post_mime_types;
	}

	public static function register() {
		if ( taxonomy_exists( FIGURO_MEDIA_TAXONOMY ) ) {
			return;
		}

		register_taxonomy(
			FIGURO_MEDIA_TAXONOMY,
			'attachment',
			array(
				'label'             => __( 'Folders', 'figuro-media' ),
				'hierarchical'      => true,
				'public'            => false,
				'show_ui'           => false,
				'show_admin_column' => false,
				'show_in_rest'      => false,
				'query_var'         => false,
				'rewrite'           => false,
			)
		);
	}

	public static function create_folder( $name, $parent = 0 ) {
		$name = trim( wp_strip_all_tags( $name ) );

		if ( '' === $name ) {
			return new WP_Error( 'figuro_empty_name', __( 'Folder name cannot be empty.', 'figuro-media' ) );
		}

		return wp_insert_term( $name, FIGURO_MEDIA_TAXONOMY, array( 'parent' => (int) $parent ) );
	}

	public static function rename_folder( $id, $name ) {
		$name = trim( wp_strip_all_tags( $name ) );

		if ( '' === $name ) {
			return new WP_Error( 'figuro_empty_name', __( 'Folder name cannot be empty.', 'figuro-media' ) );
		}

		return wp_update_term( (int) $id, FIGURO_MEDIA_TAXONOMY, array( 'name' => $name ) );
	}

	public static function move_folder( $id, $new_parent ) {
		$id         = (int) $id;
		$new_parent = (int) $new_parent;

		if ( $id === $new_parent ) {
			return new WP_Error( 'figuro_invalid_parent', __( 'A folder cannot be its own parent.', 'figuro-media' ) );
		}

		if ( $new_parent && self::is_descendant( $new_parent, $id ) ) {
			return new WP_Error( 'figuro_invalid_parent', __( 'Cannot move a folder into its own subfolder.', 'figuro-media' ) );
		}

		return wp_update_term( $id, FIGURO_MEDIA_TAXONOMY, array( 'parent' => $new_parent ) );
	}

	public static function is_descendant( $maybe_child_id, $of_folder_id ) {
		$ancestors = get_ancestors( $maybe_child_id, FIGURO_MEDIA_TAXONOMY, 'taxonomy' );
		return in_array( (int) $of_folder_id, array_map( 'intval', $ancestors ), true );
	}

	public static function delete_folder( $id ) {
		return wp_delete_term( (int) $id, FIGURO_MEDIA_TAXONOMY );
	}

	/**
	 * Nested folder tree (id, name, parent, count, children[]) ordered by name.
	 */
	public static function get_tree() {
		$terms = get_terms(
			array(
				'taxonomy'   => FIGURO_MEDIA_TAXONOMY,
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return array();
		}

		$counts = self::get_real_counts( wp_list_pluck( $terms, 'term_id' ) );

		$by_parent = array();
		foreach ( $terms as $term ) {
			$by_parent[ (int) $term->parent ][] = array(
				'id'     => (int) $term->term_id,
				'name'   => $term->name,
				'parent' => (int) $term->parent,
				'count'  => isset( $counts[ (int) $term->term_id ] ) ? $counts[ (int) $term->term_id ] : 0,
			);
		}

		// Count each folder like a file manager would: its own files plus
		// everything inside its subfolders, so a parent's number always adds
		// up to the sum of what you find by drilling into its children.
		$build = function ( $parent_id ) use ( &$build, $by_parent ) {
			$branch = array();

			if ( empty( $by_parent[ $parent_id ] ) ) {
				return $branch;
			}

			foreach ( $by_parent[ $parent_id ] as $node ) {
				$node['children']  = $build( $node['id'] );
				$node['count']    += array_sum( wp_list_pluck( $node['children'], 'count' ) );
				$branch[]           = $node;
			}

			return $branch;
		};

		return $build( 0 );
	}

	/**
	 * Folder counts, computed directly rather than via WordPress's cached
	 * term count: core only counts attachments that have a published parent
	 * post, but most media-library uploads are never attached to one.
	 *
	 * @param int[] $term_ids
	 * @return array<int,int> term_id => attachment count
	 */
	private static function get_real_counts( array $term_ids ) {
		global $wpdb;

		if ( empty( $term_ids ) ) {
			return array();
		}

		$term_ids     = array_map( 'intval', $term_ids );
		$placeholders = implode( ',', array_fill( 0, count( $term_ids ), '%d' ) );

		$sql = "
			SELECT tt.term_id, COUNT(*) AS cnt
			FROM {$wpdb->term_relationships} tr
			INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
			INNER JOIN {$wpdb->posts} p ON p.ID = tr.object_id
			WHERE tt.taxonomy = %s
			AND p.post_type = 'attachment'
			AND tt.term_id IN ( {$placeholders} )
			GROUP BY tt.term_id
		";

		$rows = $wpdb->get_results( $wpdb->prepare( $sql, array_merge( array( FIGURO_MEDIA_TAXONOMY ), $term_ids ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		$counts = array();
		foreach ( (array) $rows as $row ) {
			$counts[ (int) $row->term_id ] = (int) $row->cnt;
		}

		return $counts;
	}

	/**
	 * Counts for the pinned "All Files" and "Uncategorized" views.
	 *
	 * @return array{all:int,uncategorized:int}
	 */
	public static function get_totals() {
		global $wpdb;

		$all = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_status = 'inherit'"
		);

		$uncategorized = (int) $wpdb->get_var(
			$wpdb->prepare(
				"
				SELECT COUNT(*) FROM {$wpdb->posts} p
				WHERE p.post_type = 'attachment' AND p.post_status = 'inherit'
				AND NOT EXISTS (
					SELECT 1 FROM {$wpdb->term_relationships} tr
					INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
					WHERE tr.object_id = p.ID AND tt.taxonomy = %s
				)
				",
				FIGURO_MEDIA_TAXONOMY
			)
		);

		return array(
			'all'           => $all,
			'uncategorized' => $uncategorized,
		);
	}

	/**
	 * @param int|string $folder_id '' for all, 'uncategorized', or a term_id.
	 * @param array      $filters   Optional. Mirrors the core Media Library's
	 *                              "All media items" / "All dates" dropdowns:
	 *                              mime_type (string), uploaded_to (int, 0 = unattached),
	 *                              author (int), year (int), monthnum (int).
	 */
	public static function get_attachments( $folder_id, $paged = 1, $per_page = 60, $search = '', array $filters = array() ) {
		$args = array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => $per_page,
			'paged'          => $paged,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		if ( '' !== $search ) {
			$args['s'] = $search;
		}

		if ( ! empty( $filters['mime_type'] ) ) {
			$args['post_mime_type'] = $filters['mime_type'];
		}

		if ( isset( $filters['uploaded_to'] ) && null !== $filters['uploaded_to'] ) {
			$args['post_parent'] = (int) $filters['uploaded_to'];
		}

		if ( ! empty( $filters['author'] ) ) {
			$args['author'] = (int) $filters['author'];
		}

		if ( ! empty( $filters['year'] ) ) {
			$args['year'] = (int) $filters['year'];
		}

		if ( ! empty( $filters['monthnum'] ) ) {
			$args['monthnum'] = (int) $filters['monthnum'];
		}

		if ( 'uncategorized' === $folder_id ) {
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => FIGURO_MEDIA_TAXONOMY,
					'operator' => 'NOT EXISTS',
				),
			);
		} elseif ( $folder_id ) {
			// Browsing a folder also shows files placed in its subfolders,
			// matching the aggregated counts shown in the folder tree.
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => FIGURO_MEDIA_TAXONOMY,
					'field'    => 'term_id',
					'terms'    => (int) $folder_id,
				),
			);
		}

		return new WP_Query( $args );
	}

	/**
	 * FileBird keeps one folder per file; we mirror that by replacing, not adding.
	 */
	public static function set_attachment_folder( $attachment_id, $folder_id ) {
		$attachment_id = (int) $attachment_id;

		if ( 'attachment' !== get_post_type( $attachment_id ) ) {
			return new WP_Error( 'figuro_invalid_attachment', __( 'Invalid attachment.', 'figuro-media' ) );
		}

		if ( ! $folder_id ) {
			return wp_set_object_terms( $attachment_id, array(), FIGURO_MEDIA_TAXONOMY );
		}

		return wp_set_object_terms( $attachment_id, array( (int) $folder_id ), FIGURO_MEDIA_TAXONOMY );
	}

	/**
	 * Injects a "Folder" <select> into the Attachment Details view (core's
	 * `attachment_fields_to_edit` extension point). Named `figuro_folder_id`
	 * rather than the taxonomy's own slug so core's generic, comma-separated
	 * taxonomy-field handling in wp_ajax_save_attachment_compat() doesn't
	 * also try to process it — save_folder_field() below handles it instead.
	 *
	 * @param array   $form_fields
	 * @param WP_Post $post
	 * @return array
	 */
	public static function add_folder_field( $form_fields, $post ) {
		if ( 'attachment' !== $post->post_type ) {
			return $form_fields;
		}

		$current  = wp_get_object_terms( $post->ID, FIGURO_MEDIA_TAXONOMY, array( 'fields' => 'ids' ) );
		$current  = is_wp_error( $current ) ? array() : $current;
		$selected = ! empty( $current ) ? (int) $current[0] : 0;

		$options = '<option value="0"' . selected( $selected, 0, false ) . '>' . esc_html__( 'Uncategorized', 'figuro-media' ) . '</option>';

		foreach ( self::flatten_tree( self::get_tree() ) as $node ) {
			$options .= sprintf(
				'<option value="%1$d"%2$s>%3$s%4$s</option>',
				$node['id'],
				selected( $selected, $node['id'], false ),
				str_repeat( '&nbsp;&nbsp;&nbsp;', $node['depth'] ),
				esc_html( $node['name'] )
			);
		}

		$form_fields['figuro_folder_id'] = array(
			'label' => __( 'Folder', 'figuro-media' ),
			'input' => 'html',
			'html'  => '<select name="attachments[' . (int) $post->ID . '][figuro_folder_id]" id="attachments-' . (int) $post->ID . '-figuro_folder_id">' . $options . '</select>',
		);

		return $form_fields;
	}

	/**
	 * Persists the "Folder" field added by add_folder_field() above.
	 *
	 * @param array $post
	 * @param array $attachment
	 * @return array
	 */
	public static function save_folder_field( $post, $attachment ) {
		if ( isset( $attachment['figuro_folder_id'] ) ) {
			self::set_attachment_folder( $post['ID'], absint( $attachment['figuro_folder_id'] ) );
		}

		return $post;
	}

	/**
	 * Flattens the nested folder tree into a depth-ordered list for <option> rendering.
	 *
	 * @param array $nodes
	 * @param int   $depth
	 * @return array
	 */
	private static function flatten_tree( $nodes, $depth = 0 ) {
		$flat = array();

		foreach ( $nodes as $node ) {
			$flat[] = array(
				'id'    => $node['id'],
				'name'  => $node['name'],
				'depth' => $depth,
			);

			if ( ! empty( $node['children'] ) ) {
				$flat = array_merge( $flat, self::flatten_tree( $node['children'], $depth + 1 ) );
			}
		}

		return $flat;
	}
}
