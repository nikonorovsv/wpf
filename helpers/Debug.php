<?php

namespace wpf\helpers;

/**
 * Class Debug
 * @package wpf\helpers
 */
class Debug {
	/**
	 * @return bool
	 */
	public static function isDebug() {
		return defined('WP_DEBUG') && WP_DEBUG;
	}

	/**
	 * @param array ...$object
	 *
	 * @return bool
	 */
	public static function varDump( ...$object ) {
		if ( ! self::isDebug() ) {
			return FALSE;
		}
		if ( ! is_user_logged_in() ) {
			return FALSE;
		}
		echo '<pre>';
		var_dump( ...$object );
		echo '</pre>';
	}

	/**
	 * @param $object
	 *
	 * @return bool
	 */
	public static function varExport( $object ) {
		if ( ! self::isDebug() ) {
			return FALSE;
		}
		if ( ! is_user_logged_in() ) {
			return FALSE;
		}
		echo '<pre>';
		var_export( $object );
		echo '</pre>';
	}

    /**
     * @param string $msg
     * @param string $title
     * @param string $file
     * @return bool
     */
	public static function logIt( string $msg, string $title, string $file ) {
		if ( ! self::isDebug() ) {
			return FALSE;
		}
		error_log( join(' | ', [
			current_time('d.m.Y h:i:s'),
			$title,
			print_r( $msg, TRUE ) . "\n"
		] ), 3, WP::path( $file ) );
	}
}