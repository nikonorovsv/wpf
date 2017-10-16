<?php

namespace wpf\wp;

use \wpf\helpers\Cache;
use \WP_Query;

/**
 * Class QueryBuilder
 * @package wpf\wp
 */
trait QueryBuilder {

	use Cache;

	/**
	 * @return WP_Query
	 */
	final public function query(): WP_Query {
		$args     = $this->queryArgs();
		$fallback = function () use ( $args ) {
			return new WP_Query( $args );
		};

		return static::cache( $fallback, serialize( $args ) );
	}

	/**
	 * @return array
	 */
	protected function queryArgs(): array {
		return [];
	}

	/**
	 * The passed object will be set as global $wp_query
	 *
	 * @param WP_Query $query
	 */
	public static function setGlobalQuery( WP_Query $query ) {
		global $wp_query;
		$wp_query = $query;

		return;
	}

	/**
	 * @return array
	 */
	final public function posts(): array {
		return $this->query()->posts;
	}
}