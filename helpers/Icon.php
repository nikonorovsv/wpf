<?php
namespace wpf\helpers;

/**
 * Class Icon
 *
 * @method static $this string fa( string $name, array $options )
 * @package wpf\helpers
 */
class Icon {
	/**
	 * @var string
	 */
	public static $tag_name = 'i';
	/**
	 * @var array
	 */
	public static $prefixes
		= [
			'fa' => 'fa fa-',
			'gi' => 'glyphicon glyphicon-',
			'ui' => 'icon ',
			'wp' => 'dashicons dashicons-'
		];
	
	/**
	 * @param $family
	 * @param $name
	 * @param array $options
	 *
	 * @return string
	 */
	public static function familyIcon( $family, $name, $options = [] ) {
		$options[ 'prefix' ] = self::getPrefix( $family );
		
		return self::i( $name, $options );
	}
	
	/**
	 * @param $name
	 *
	 * @return string
	 */
	public static function getPrefix( $name ) {
		return self::$prefixes[ $name ] ?? '';
	}
	
	/**
	 * Вывод иконки
	 *
	 * @param $name
	 * @param array $options
	 *
	 * @return string
	 */
	public static function i( $name, $options = [] ) {
		$tag    = ArrayHelper::remove( $options, 'tag', self::$tag_name );
		$prefix = ArrayHelper::remove( $options, 'prefix' );
		Html::addCssClass( $options, $prefix . $name );
		
		return Html::tag( $tag, '', $options );
	}
	
	/**
	 * @param $name
	 * @param $arguments
	 *
	 * @return mixed
	 */
	public static function __callStatic( $name, $arguments ) {
		array_unshift( $arguments, $name );
		
		// TODO
		return call_user_func_array( '\wpf\helpers\Icon::familyIcon', $arguments );
	}
}