<?php
/**
 * Plugin Name: Media Offloader / Regenerate Thumbnails compat
 * Description: Stops Advanced Media Offloader from downloading originals from R2 every time the Media Library renders.
 *
 * The offloader decides a request is "regenerating thumbnails" when any function in the
 * call stack contains both "regenerat" and "thumbnail". The Regenerate Thumbnails plugin
 * matches through its class name, and it runs on every attachment the Media Library
 * renders (modal fields, list-view row actions, edit screen). Result: opening the library
 * pulled each original back into uploads/. Real regenerations don't go through these UI
 * hooks, so they keep working.
 */

add_filter(
	'advmo_is_thumbnail_regeneration_request',
	static function ( $detected ) {
		if ( ! $detected ) {
			return $detected;
		}

		$ui_callbacks = array(
			'add_button_to_edit_media_modal_fields_area',
			'add_button_to_edit_media_page',
			'add_regenerate_link_to_media_list_view',
		);

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace
		foreach ( debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 25 ) as $frame ) {
			if ( isset( $frame['function'] ) && in_array( $frame['function'], $ui_callbacks, true ) ) {
				return false;
			}
		}

		return $detected;
	}
);
