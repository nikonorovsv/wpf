<?php

namespace wpf\wp\facades;

use WP_Site;
use WP_Error;
use wpf\helpers\WP;

/**
 * Class Blog
 * @package app\wp\facades
 */
class Blog
  implements IFacade
{
  /**
   * @var WP_Site
   */
  private $blog;

  /**
   * Blog constructor.
   *
   * @param int|NULL $blog
   */
  public function __construct(int $blog = NULL)
  {
    $blog = $blog ?? get_current_blog_id();
    $this->blog = WP_Site::get_instance($blog);
  }

    /**
     * @param string $name
     *
     * @return bool|false|mixed|null|string
     */
  public function __get(string $name)
  {
    $value = NULL;
    if ($this->blog->$name) :
      return $this->blog->$name;
    elseif ($value = get_bloginfo($name)) :
    elseif ($value = get_option($name)) :
    elseif ($value = get_field($name, 'option')) :
    endif;

    return $value;
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
    return $this->blogname;
  }

  /**
   * @param array $args
   *
   * @return string
   */
  public function url(array $args = []): string
  {
    $url = $this->siteurl;
    if ($args) {
      $url = add_query_arg($args, $url);
    }

    return $url;
  }

  /**
   * @param string $path
   * @param string $scheme
   *
   * @return string
   */
  public function editUrl(string $path = '', string $scheme = 'admin'): string
  {
    return get_admin_url($this->id, $path, $scheme);
  }

  /**
   * @param string $name
   * @param $value
   *
   * @return bool|int|WP_Error
   */
  public function update(string $name, $value): WP_Error|bool|int
  {
    if (get_field($name, 'option')) {
      return update_field($name, $value, 'option');
    } elseif (get_option($name)) {
      return update_option($name, $value);
    }

    return FALSE;
  }

  /**
   * @param array $data
   * @return int|WP_Error
   */
  public static function create(array $data): WP_Error|int
  {
    if (!isset($data['domain'])) {
      return WP::error('missing_domain', "The 'domain' parameter can't sent.");
    }
    if (!isset($data['path'])) {
      return WP::error('missing_path', "The 'path' parameter can't sent.");
    }
    if (!isset($data['title'])) {
      return WP::error('missing_title', "The 'title' parameter can't sent.");
    }
    if (!isset($data['user_id'])) {
      return WP::error('missing_user_id', "The 'user_id' parameter can't sent.");
    }

    return wpmu_create_blog(
      $data['domain'],
      $data['path'],
      $data['title'],
      $data['user_id'],
      $data['meta'] ?? [],
      $data['network_id'] ?? 1
    );
  }
}