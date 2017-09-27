<?php

namespace wpf\base;

use \wpf\base\Request;
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
	 * @var \wpf\base\Request
	 */
	protected $request;

	/**
	 * AjaxHandler constructor.
	 */
	public function __construct() {
		$this->request = new Request();
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