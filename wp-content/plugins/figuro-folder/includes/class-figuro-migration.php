<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * One-time import of the folder structure from FileBird (free or Pro),
 * so nothing has to be re-organized by hand after switching plugins.
 * Reads FileBird's own tables directly; never touches them.
 */
class Figuro_Migration {

	const OPTION_KEY = 'figuro_media_migrated_from_filebird';

	public static function maybe_migrate_on_activation() {
		Figuro_Taxonomy::register();

		if ( get_option( self::OPTION_KEY ) ) {
			return;
		}

		self::run();
	}

	public static function run() {
		global $wpdb;

		Figuro_Taxonomy::register();

		$folders_table = $wpdb->prefix . 'fbv';
		$rel_table     = $wpdb->prefix . 'fbv_attachment_folder';

		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $folders_table ) ) !== $folders_table ) {
			update_option(
				self::OPTION_KEY,
				array(
					'status' => 'skipped',
					'reason' => 'no_filebird_tables',
					'time'   => time(),
				)
			);
			return;
		}

		$rows = $wpdb->get_results( "SELECT id, name, parent, ord FROM `{$folders_table}` ORDER BY parent ASC, ord ASC, id ASC" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		if ( empty( $rows ) ) {
			update_option(
				self::OPTION_KEY,
				array(
					'status' => 'skipped',
					'reason' => 'no_folders',
					'time'   => time(),
				)
			);
			return;
		}

		$id_map     = array(); // old fbv.id => new term_id
		$pending    = $rows;
		$max_passes = count( $rows ) + 1;

		// Rows are ordered parent-first, but loop defensively in case of gaps.
		while ( $pending && $max_passes-- > 0 ) {
			$next = array();

			foreach ( $pending as $row ) {
				$parent_old = (int) $row->parent;

				if ( 0 !== $parent_old && ! isset( $id_map[ $parent_old ] ) ) {
					$next[] = $row;
					continue;
				}

				$parent_new = $parent_old ? $id_map[ $parent_old ] : 0;
				$name       = ( '' !== $row->name ) ? $row->name : sprintf( 'Folder %d', $row->id );

				$term = wp_insert_term( $name, FIGURO_MEDIA_TAXONOMY, array( 'parent' => $parent_new ) );

				if ( is_wp_error( $term ) ) {
					if ( 'term_exists' === $term->get_error_code() ) {
						$existing_id              = $term->get_error_data();
						$id_map[ (int) $row->id ] = is_array( $existing_id ) ? (int) $existing_id['term_id'] : (int) $existing_id;
					}
					continue;
				}

				$id_map[ (int) $row->id ] = (int) $term['term_id'];
			}

			$pending = $next;
		}

		$relations = $wpdb->get_results( "SELECT folder_id, attachment_id FROM `{$rel_table}`" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		$by_attachment = array();
		foreach ( (array) $relations as $rel ) {
			$old_folder = (int) $rel->folder_id;

			if ( ! isset( $id_map[ $old_folder ] ) ) {
				continue;
			}

			// FileBird's default UI keeps one folder per file; keep the first match.
			if ( ! isset( $by_attachment[ (int) $rel->attachment_id ] ) ) {
				$by_attachment[ (int) $rel->attachment_id ] = $id_map[ $old_folder ];
			}
		}

		$moved = 0;
		foreach ( $by_attachment as $attachment_id => $term_id ) {
			if ( 'attachment' !== get_post_type( $attachment_id ) ) {
				continue;
			}

			$result = wp_set_object_terms( $attachment_id, array( $term_id ), FIGURO_MEDIA_TAXONOMY );

			if ( ! is_wp_error( $result ) ) {
				$moved++;
			}
		}

		self::recount( $id_map );

		update_option(
			self::OPTION_KEY,
			array(
				'status'          => 'done',
				'folders_created' => count( array_unique( $id_map ) ),
				'attachments'     => $moved,
				'time'            => time(),
			)
		);
	}

	/**
	 * wp_set_object_terms() updates term counts as it goes, but that relies on
	 * a count callback being resolved consistently for a taxonomy registered
	 * mid-request; force a recalculation so folder counts are correct right away.
	 */
	private static function recount( array $id_map ) {
		$term_taxonomy_ids = array();

		foreach ( array_unique( $id_map ) as $term_id ) {
			$term = get_term( $term_id, FIGURO_MEDIA_TAXONOMY );
			if ( $term && ! is_wp_error( $term ) ) {
				$term_taxonomy_ids[] = $term->term_taxonomy_id;
			}
		}

		if ( $term_taxonomy_ids ) {
			wp_update_term_count_now( array_unique( $term_taxonomy_ids ), FIGURO_MEDIA_TAXONOMY );
		}
	}
}
