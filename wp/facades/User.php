<?php

namespace wpf\wp\facades;

use WP_User;
use WP_Error;
use wpf\helpers\WP;

/**
 * Class User
 * @package app\wp\facades
 */
class User
    implements IFacade
{
    /**
     * @var WP_User|null
     */
    private ?WP_User $_user;
    private string $_key;

    /**
     * User constructor.
     *
     * @param int|NULL $user
     */
    public function __construct(int $user = NULL)
    {
        $user = $user ?? get_current_user_id();
        $this->_user = get_user_by('id', $user);
        $this->_key = "user_$user";
    }

    /**
     * @param string $name
     *
     * @return bool|false|mixed|null|string
     */
    public function __get(string $name)
    {
        if ($_name = strstr($name, '_object', TRUE)) {
            return get_field_object($_name, $this->_key);
        } elseif ($value = get_field($name, $this->_key)) {
            return $value;
        }
        return $this->_user->$name ?? NULL;
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
        return $this->$name ? get_field_object($name, $this->_key)['label'] : '';
    }

    /**
     * @param array $args
     *
     * @return string
     */
    public function url(array $args = []): string
    {
        $url = get_author_posts_url($this->ID);
        if ($args) {
            $url = add_query_arg($args, $url);
        }

        return $url;
    }

    /**
     * @param string $size
     *
     * @return string
     */
    public function thumbUrl(string $size = 'thumbnail'): string
    {
        $size = WP::getImageSize($size);

        return get_avatar_url($this->ID, ['size' => $size['width']]);
    }

    /**
     * @return string
     */
    public function editUrl(): string
    {
        return get_edit_user_link($this->ID);
    }

    /**
     * @param string $name
     * @param $value
     *
     * @return bool|int|WP_Error
     */
    public function update(string $name, $value): bool|int|WP_Error
    {
        if ($this->_user->$name) {
            return wp_update_user([
                'ID' => $this->ID,
                $name => $value
            ]);
        } elseif ($this->$name) {
            return update_field($name, $value, $this->_key);
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
        // ToDo Change with  wp_create_user()
        return wp_insert_user($data);
    }
}