<?php

namespace wpf\base;

use \wpf\wp\QueryBuilder;

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
}