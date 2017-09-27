<?php
namespace wpf\wp;

use \wpf\base\Entity;

/**
 * Class PostType
 * @package wpf\wp
 */
abstract class PostType extends Entity
{
	protected $args = [];
	
	/**
	 * @return bool
	 */
	public function register() {
		if ( ! $this->args ) {
			return FALSE;
		}
		register_post_type( static::NAME, $this->args );
	}
}