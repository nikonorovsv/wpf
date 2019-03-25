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
        if ( ! defined('THEME_NONCE_KEY') ) {
            static::response( FALSE, __('Constant "THEME_NONCE_KEY" has not defined.', 'wpf') );
        }
		check_ajax_referer( THEME_NONCE_KEY, static::NONCE_FIELD );
        if ( method_exists( static::class, 'validate') ) {
            $this->validate();
        }
	}

	/**
	 * @return mixed
	 */
	abstract public static function run();

    /**
     * @param bool $success
     * @param string $message
     * @param array $data
     */
    protected static function response( bool $success, string $message, array $data = [] ) {
        $data['message'] = __( $message, PREFIX );
        if ( $success ) {
            wp_send_json_success( $data );
        }
        wp_send_json_error( $data );
    }

    /**
     * @return string
     */
    public static function getName() {
        return static::class;
    }
}