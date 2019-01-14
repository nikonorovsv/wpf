<?php

namespace wpf\base;

use \wpf\helpers\WP;

/**
 * Class AjaxHandler
 * @package wpf\base
 */
abstract class AjaxHandler
{
	const NONCE_FIELD = 'nonce';
	const ACTION_NAME = NULL;

	/**
	 * AjaxHandler constructor.
	 */
	public function __construct() {
		check_ajax_referer( THEME_NONCE_KEY, static::NONCE_FIELD );
	}

	/**
	 * @return mixed
	 */
	abstract public static function run();

	/**
	 * @return string
	 */
	public static function getName() {
		return static::class;
	}

    /**
     * @param bool $success
     * @param string $message
     * @param array $data
     */
    protected static function response( bool $success, string $message, array $data = [] ) {
        $data['message'] = __( $message, PREFIX );
        return $success ? wp_send_json_success( $data ) : wp_send_json_error( $data );
    }

    /**
     * @param string $nonce
     */
    protected static function checkNonce( string $nonce ) {
        if ( ! defined( THEME_NONCE_KEY ) ) {
            self::response( false, __('Constant THEME_NONCE_KEY has not defined.', 'wpf'));
        } elseif ( ! wp_verify_nonce( $nonce, THEME_NONCE_KEY ) ) {
            self::response( false, __('Incorrect nonce key.', 'wpf'));
        }
    }
}