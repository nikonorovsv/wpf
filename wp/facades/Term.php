<?php

namespace wpf\wp\facades;

use WP_Term;
use wpf\helpers\ArrayHelper;
use wpf\helpers\WP;

/**
 * Class Term
 * @package wpf\wp\facades
 */
class Term
    implements IFacade
{

    /**
     * @var WP_Term object
     */
    private $term;

    /**
     * Term constructor.
     *
     * @param int|WP_Term $term Optional. Term ID or WP_Term object.
     */
    public function __construct($term = NULL)
    {
        $this->term = get_term($term);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        if ($_name = strstr($name, '_object', TRUE)) {
            return get_field_object($_name, $this->term);
        } elseif ($value = get_field($name, $this->term)) {
            return $value;
        }

        return $this->term->$name ?? NULL;
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
        return $this->$name ? get_field_object($name, $this->term)['label'] : '';
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->name;
    }

    /**
     * @param array $args
     *
     * @return string
     */
    public function url(array $args = []): string
    {
        $url = get_term_link($this->term);
        if ($args) {
            $url = add_query_arg($args, $url);
        }

        return $url;
    }

    /**
     * @return string
     */
    public function editUrl(): string
    {
        return get_edit_term_link($this->term_id);
    }

    /**
     * @param string $name
     * @param $value
     *
     * @return bool|int|WP_Error
     */
    public function update(string $name, $value): WP_Error|bool|int
    {
        if ($this->term->$name) {
            return wp_update_term($this->term_id, $this->taxonomy, [
                $name => $value
            ]);
        } elseif ($this->$name) {
            return update_field($name, $value, $this->term);
        }

        return FALSE;
    }

    /**
     * @return array
     */
    public function parents(): array
    {
        return get_ancestors($this->term_id, $this->taxonomy, 'taxonomy');
    }

    /**
     * @param array $data
     *
     * @return int|WP_Error
     */
    public static function create(array $data): WP_Error|int
    {
        $taxonomy = ArrayHelper::remove($data, 'taxonomy');
        if (is_null($taxonomy)) {
            return WP::error('missing_taxonomy', "The 'taxonomy' parameter can't sent.");
        }
        $term = ArrayHelper::remove($data, 'name');
        if (is_null($term)) {
            return WP::error('missing_name', "The 'name' parameter can't sent.");
        }

        return wp_insert_term($term, $taxonomy, $data);
    }
}