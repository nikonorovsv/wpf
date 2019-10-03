<?php

namespace wpf\wp;

use wpf\helpers\Cache;
use WP_Comment_Query;

/**
 * Class CommentsBuilder.
 *
 * @package wpf\wp
 */
trait CommentsBuilder
{
    use Cache;

    /**
     * @return WP_Comment_Query
     */
    final public function commentsQuery(): WP_Comment_Query
    {
        $args = $this->commentsArgs();
        $fallback = function () use ($args) {
            return new WP_Comment_Query($args);
        };

        return static::cache($fallback, serialize($args));
    }

    /**
     * @return array
     */
    protected function commentsArgs(): array
    {
        return [];
    }

    /**
     * @return array
     */
    final public function comments(): array
    {
        return $this->commentsQuery()->comments;
    }

    /**
     * @return int
     */
    final public function commentsCount(): int
    {
        return (int) $this->commentsQuery()->found_comments;
    }
}
