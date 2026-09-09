<?php
/**
 * Plugin Name: Local Dev – Proxy live-site PDFs for flipbooks (dFlip)
 * Description: A browser can display a cross-origin image but cannot fetch() a
 *              cross-origin PDF, so dFlip / DearFlip flipbooks whose PDF lives on
 *              the production server fail on localhost with a "CROSS ORIGIN"
 *              error. This streams those PDFs through the local site (same
 *              origin, with HTTP Range support) and repoints the dFlip book at
 *              the local URL. Companion to local-image-urls.php. Delete to disable.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Local_Dev_PDF_Proxy {

	const LIVE_HOST    = 'digitalexhibition.arch.tue.nl';
	const LIVE_UPLOADS = 'https://digitalexhibition.arch.tue.nl/wp-content/uploads/';
	const QUERY_VAR    = 'figuro_pdf';
	const CACHE_SUBDIR = '_figuro-pdf-cache';

	public static function init() {
		// No-op if this ever runs on the production site itself.
		if ( self::LIVE_HOST === wp_parse_url( home_url(), PHP_URL_HOST ) ) {
			return;
		}

		add_action( 'init', array( __CLASS__, 'maybe_serve' ), 1 );
		add_action( 'template_redirect', array( __CLASS__, 'start_buffer' ), 1 );
	}

	/** Local URL that proxies a given uploads-relative PDF path. */
	public static function proxy_url( $relative_pdf_path ) {
		return home_url( '/?' . self::QUERY_VAR . '=' . rawurlencode( $relative_pdf_path ) );
	}

	/* ---- Repoint dFlip's PDF URL at the local proxy ---------------------- */

	public static function start_buffer() {
		if ( is_admin() ) {
			return;
		}
		ob_start( array( __CLASS__, 'rewrite_html' ) );
	}

	public static function rewrite_html( $html ) {
		if ( ! is_string( $html )
			|| false === stripos( $html, self::LIVE_HOST )
			|| false === stripos( $html, '.pdf' ) ) {
			return $html;
		}

		// Only touch the dFlip book's "source" value (plain or JSON-escaped slashes).
		return preg_replace_callback(
			'#("source"\s*:\s*")([^"]+?\.pdf)(")#i',
			static function ( $m ) {
				$escaped = ( false !== strpos( $m[2], '\/' ) );
				$url     = $escaped ? str_replace( '\/', '/', $m[2] ) : $m[2];

				$live = '#^https?://' . preg_quote( self::LIVE_HOST, '#' ) . '/wp-content/uploads/(.+\.pdf)$#i';
				if ( ! preg_match( $live, $url, $mm ) ) {
					return $m[0];
				}

				$proxy = self::proxy_url( $mm[1] );
				if ( $escaped ) {
					$proxy = str_replace( '/', '\/', $proxy );
				}

				return $m[1] . $proxy . $m[3];
			},
			$html
		);
	}

	/* ---- Serve the PDF same-origin ------------------------------------- */

	public static function maybe_serve() {
		if ( ! isset( $_GET[ self::QUERY_VAR ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$rel = ltrim( str_replace( '\\', '/', wp_unslash( $_GET[ self::QUERY_VAR ] ) ), '/' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( false !== strpos( $rel, '..' )
			|| ! preg_match( '#^[A-Za-z0-9_\-./ ()%&,+\'\[\]]+\.pdf$#', $rel ) ) {
			status_header( 400 );
			exit;
		}

		$cache_dir  = trailingslashit( WP_CONTENT_DIR ) . 'uploads/' . self::CACHE_SUBDIR;
		$cache_file = $cache_dir . '/' . md5( $rel ) . '.pdf';

		if ( ! file_exists( $cache_file ) || 0 === (int) @filesize( $cache_file ) ) {
			wp_mkdir_p( $cache_dir );

			$remote = self::LIVE_UPLOADS . implode( '/', array_map( 'rawurlencode', explode( '/', $rel ) ) );

			$resp = wp_remote_get(
				$remote,
				array(
					'timeout'    => 60,
					'stream'     => true,
					'filename'   => $cache_file,
					'user-agent' => 'LocalDevPDFProxy/1.0',
				)
			);

			if ( is_wp_error( $resp ) || 200 !== (int) wp_remote_retrieve_response_code( $resp ) ) {
				if ( file_exists( $cache_file ) ) {
					@unlink( $cache_file );
				}
				status_header( 502 );
				echo 'PDF proxy: could not fetch the source file from the live site.';
				exit;
			}
		}

		self::stream( $cache_file, basename( $rel ) );
	}

	private static function stream( $file, $download_name ) {
		$size  = (int) filesize( $file );
		$start = 0;
		$end   = $size - 1;
		$code  = 200;

		if ( isset( $_SERVER['HTTP_RANGE'] )
			&& preg_match( '/bytes=(\d*)-(\d*)/i', sanitize_text_field( wp_unslash( $_SERVER['HTTP_RANGE'] ) ), $r ) ) {
			if ( '' !== $r[1] ) {
				$start = (int) $r[1];
			}
			if ( '' !== $r[2] ) {
				$end = (int) $r[2];
			}
			if ( '' === $r[1] && '' !== $r[2] ) {
				$start = max( 0, $size - (int) $r[2] );
				$end   = $size - 1;
			}
			$start = max( 0, $start );
			$end   = min( $end, $size - 1 );

			if ( $start > $end ) {
				status_header( 416 );
				header( 'Content-Range: bytes */' . $size );
				exit;
			}
			$code = 206;
		}

		$length = $end - $start + 1;

		while ( ob_get_level() > 0 ) {
			ob_end_clean();
		}

		status_header( $code );
		header( 'Content-Type: application/pdf' );
		header( 'Accept-Ranges: bytes' );
		header( 'Content-Length: ' . $length );
		header( 'Content-Disposition: inline; filename="' . $download_name . '"' );
		header( 'Cache-Control: public, max-age=86400' );
		if ( 206 === $code ) {
			header( "Content-Range: bytes {$start}-{$end}/{$size}" );
		}

		$fh = fopen( $file, 'rb' );
		fseek( $fh, $start );
		$remaining = $length;
		while ( $remaining > 0 && ! feof( $fh ) ) {
			$buffer = fread( $fh, (int) min( 8192, $remaining ) );
			echo $buffer; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$remaining -= strlen( $buffer );
			flush();
		}
		fclose( $fh );
		exit;
	}
}

Local_Dev_PDF_Proxy::init();
