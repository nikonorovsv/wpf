<?php

namespace wpf\wp;

use \wpf\helpers\Cache;
use \WP_Term_Query;

/**
 * Trait TermQueryBuilder
 * @package wpf\wp
 */
trait TermQueryBuilder
{

    use Cache;

    /**
     * @return WP_Term_Query
     */
    final public function query(): WP_Term_Query
    {
        $args = $this->queryArgs();
        $fallback = function () use ($args) {
            return new WP_Term_Query($args);
        };

        return static::cache($fallback, serialize($args));
    }

    /**
     * @return array
     */
    protected function queryArgs(): array
    {
        return [];
    }

    /**
     * @return array
     */
    final public function terms(): array
    {
        return $this->query()->terms;
    }
}