<?php

namespace wpf\wp\facades;

use WP_Post;
use WP_Error;
use wpf\helpers\Html;
use wpf\helpers\Date;
use wpf\helpers\WP;
use Exception;

/**
 * Class Post
 * @package wpf\wp\facades
 */
class Post
    implements IFacade
{
    /**
     * @var array object
     */
    private int|WP_Post|null $post;

    /**
     * Post constructor.
     *
     * @param int|WP_Post|null $post Optional. Post ID or post object. Defaults to global $post.
     */
    public function __construct($post = NULL)
    {
        $this->post = get_post($post);
    }

    /**
     * @param string $name
     *
     * @return mixed|bool
     */
    public function __get(string $name)
    {
        if ($_name = strstr($name, '_object', TRUE)) {
            return get_field_object($_name, $this->post);
        } elseif ($value = get_field($name, $this->post)) {
            return $value;
        }

        return $this->post->$name ?? NULL;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name)
    {
        return isset($this->$name);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function label(string $name): string
    {
        return $this->$name ? get_field_object($name, $this->ID)['label'] : '';
    }

    /**
     * @param array $args
     *
     * @return string
     */
    public function url(array $args = []): string
    {
        $url = get_permalink($this->ID);
        if ($args) {
            $url = add_query_arg($args, $url);
        }

        return $url;

    }

    /**
     * @return Date
     * @throws Exception
     */
    public function pubDate(): Date
    {
        return new Date($this->post_date);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->post_title;
    }

    /**
     * @param int $length
     * @param string $more
     *
     * @return string
     */
    public function excerpt(int $length = 0, string $more = '...'): string
    {
        $excerpt = get_the_excerpt($this->ID);
        if ($length) {
            $arr = explode(' ', $excerpt);
            $arr = array_slice($arr, 0, $length);
            return join(' ', $arr) . $more;
        }

        return $excerpt;
    }

    /**
     * @return string
     */
    public function content(): string
    {
        return apply_filters('the_content', $this->post_content);
    }

    /**
     * @return int
     */
    public function countComments(): int
    {
        return get_comments_number($this->ID);
    }

    /**
     * @return string
     */
    public function editUrl(): string
    {
        $url = get_edit_post_link($this->ID);

        return $url;
    }

    /**
     * @return string
     */
    public function deleteUrl(): string
    {
        return get_delete_post_link($this->ID);
    }

    /**
     * @param string $name
     * @param $value
     *
     * @return bool|int|WP_Error
     */
    public function update(string $name, $value): WP_Error|bool|int
    {
        if ($this->post->$name) {
            return wp_update_post([
                'ID' => $this->ID,
                $name => $value
            ]);
        } elseif ($this->$name) {
            return update_field($name, $value, $this->ID);
        }

        return FALSE;
    }

    /**
     * @param array $data
     *
     * @return int|WP_Error
     */
    public static function create(array $data): WP_Error|int
    {
        return wp_insert_post(wp_slash($data));
    }

    /**
     * @param $taxonomies
     * @param array $args
     *
     * @return array|WP_Error
     */
    public function terms($taxonomies, array $args = []): WP_Error|array
    {
        return wp_get_object_terms($this->ID, $taxonomies, wp_parse_args($args, [
            'fields' => 'all'
        ]));
    }

    /**
     * @param string $taxonomy
     *
     * @return array
     */
    public function termLinks(string $taxonomy): array
    {
        $res = [];
        $terms = $this->terms($taxonomy);
        if (is_array($terms)) {
            foreach ($terms as $term) {
                $res[] = Html::a($term->name, get_term_link($term->term_id));
            }
        }

        return $res;
    }

    /**
     * @return bool|string
     */
    public function thumbCaption(): string
    {
        if (post_password_required($this->ID)) {
            return '';
        }
        return get_the_post_thumbnail_caption($this->ID);
    }

    /**
     * @param string $size
     * @param bool $placeholder
     *
     * @return string
     */
    public function thumbUrl(string $size = 'thumbnail', bool $placeholder = TRUE): string
    {
        if (post_password_required($this->ID)) {
            return '';
        }
        $src = get_the_post_thumbnail_url($this->ID, $size);
        if (!$src && $placeholder) {
            $size = WP::getImageSize($size);
            $text = WP::l18n("Нет+изображения");
            $src = "//placehold.it/$size[width]x$size[height]?text=$text";
        }

        return $src;
    }

    /**
     * @return bool
     */
    public function hasThumb(): bool
    {
        return has_post_thumbnail($this->ID);
    }

    /**
     * @return false|string
     */
    public function postType(): string|false
    {
        return get_post_type($this->ID);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function postTypeLabel(string $name = 'singular_name'): string
    {
        $obj = get_post_type_object($this->postType());

        return $obj->labels->$name ?? '';
    }

    /**
     * @return array
     */
    public function parents(): array
    {
        return get_post_ancestors($this->ID);
    }

    /**
     * @return int
     */
    public function depth(): int
    {
        $parent_id = $this->post_parent;
        $depth = 0;
        while ($parent_id > 0) {
            $page = get_post($parent_id);
            $parent_id = $page->post_parent;
            $depth++;
        }

        return $depth;
    }

    /**
     * @param array $args
     *
     * @return array
     */
    public function children(array $args = []): array
    {
        $args = wp_parse_args($args, [
            'post_parent' => $this->ID,
            'fields' => 'ids',
            'orderby' => 'menu_order',
            'order' => 'ASC'
        ]);

        return get_children($args);
    }
}
