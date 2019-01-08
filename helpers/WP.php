<?php

namespace wpf\helpers;

use \WP_Error;

/**
 * Class WP
 * @package wpf\helpers
 */
class WP {
	/**
	 * @param int|NULL $site_id
	 *
	 * @return bool
	 */
	public static function isMainSite( int $site_id = null ) {
		return is_main_site( $site_id );
	}

	/**
	 * @return bool
	 */
	public static function isAjax() {
		return ( defined('DOING_AJAX') && DOING_AJAX );
	}

	/**
	 * @return bool
	 */
	public static function isLoggedUser() {
		return is_user_logged_in();
	}

	/**
	 * Без аргументов вернет кореневой URI темы
	 *
	 * @param null|string $path
	 *
	 * @return mixed
	 */
	public static function uri( $path = null ) {
		return get_theme_file_uri( $path );
	}

	/**
	 * Без аргументов вернет кореневой путь темы
	 *
	 * @param null|string $path
	 *
	 * @return string
	 */
	public static function path( $path = null ) {
		return get_theme_file_path( $path );
	}

	/**
	 * @param array $args
	 * @param string|null $url
	 *
	 * @return mixed
	 */
	public static function url( array $args = [], string $url = null ) {
		if ( is_null( $url ) ) {
			$url = $_SERVER['REQUEST_URI'];
		}

		return add_query_arg( $args, $url );
	}

	/**
	 * @param $size
	 *
	 * @return mixed
	 */
	public static function getImageSize( string $size = 'thumbnail') {
		$sizes = self::getImageSizes();
		$size  = key_exists( $size, $sizes ) ? $size : 'thumbnail';

		return $sizes[ $size ];
	}

	/**
	 * @param bool $unset_disabled
	 *
	 * @return array
	 */
	public static function getImageSizes( $unset_disabled = true ) {
		$wais  = &$GLOBALS['_wp_additional_image_sizes'];
		$sizes = [];
		foreach ( get_intermediate_image_sizes() as $_size ) {
			if ( in_array( $_size, ['thumbnail', 'medium', 'medium_large', 'large'] ) ) {
				$sizes[ $_size ] = [
					'width'  => get_option("{$_size}_size_w"),
					'height' => get_option("{$_size}_size_h"),
					'crop'   => (bool) get_option("{$_size}_crop"),
				];
			} elseif ( $wais[ $_size ] ) {
				$sizes[ $_size ] = [
					'width'  => $wais[ $_size ]['width'],
					'height' => $wais[ $_size ]['height'],
					'crop'   => $wais[ $_size ]['crop'],
				];
			}
			// size registered, but has 0 width and height
			if ( $unset_disabled && ( $sizes[ $_size ]['width'] == 0 ) && ( $sizes[ $_size ]['height'] == 0 ) ) {
				unset( $sizes[ $_size ] );
			}
		}

		return $sizes;
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public static function clearString( string $string ) {
		$string = trim( $string );
		$string = stripslashes( $string );
		$string = htmlspecialchars( $string );

		return $string;
	}

	/**
	 * @param int $blog_id
	 * @param callable $callback
	 * @param array ...$args
	 *
	 * @return mixed
	 */
	public static function runOnBlog( int $blog_id, callable $callback, ...$args ) {
		switch_to_blog( $blog_id );
		$result = call_user_func_array( $callback, $args );
		switch_to_blog( get_current_blog_id() );

		return $result;
	}

	/**
	 * @return bool
	 */
	public static function logo() {
		if ( ! has_custom_logo() ) {
			return false;
		}

		return wp_get_attachment_image_url( get_theme_mod('custom_logo'), 'full');
	}

    /**
     * @param string $text
     * @return string
     */
	public static function l18n( string $text ) {
	    return __( $text, PREFIX );
    }

    /**
     * @param string $key
     * @param string $message
     * @return WP_Error
     */
    public static function error( string $key, string $message ): WP_Error {
	    return new WP_Error( $key, self::l18n( $message ) );
    }
}