<?php
namespace wpf\wp;

use \wpf\base\Entity;

/**
 * Class Taxonomy
 * @package wpf\wp
 */
abstract class Taxonomy extends Entity
{
	protected $args = [];
	protected $deps = [];
	
	/**
	 * @return bool
	 */
	public function register() {
		if ( ! ( $this->args && $this->deps ) ) {
			return FALSE;
		}
		register_taxonomy( static::NAME, $this->deps, $this->args );
	}
}