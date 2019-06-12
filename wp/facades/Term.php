<?php

namespace wpf\wp\facades;

use \WP_Term;
use \wpf\helpers\ArrayHelper;
use \wpf\helpers\WP;

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
   * @param $name
   *
   * @return mixed|bool
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
   * @param $name
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
   * @return bool
   */
  public function label(string $name)
  {
    return $this->$name ? get_field_object($name, $this->term)['label'] : FALSE;
  }

  /**
   * @return bool|mixed
   */
  public function title()
  {
    return $this->name;
  }

  /**
   * @param array $args
   *
   * @return mixed
   */
  public function url(array $args = [])
  {
    $url = get_term_link($this->term);
    if ($args) {
      $url = add_query_arg($args, $url);
    }

    return $url;
  }

  /**
   * @return mixed
   */
  public function editUrl()
  {
    return get_edit_term_link($this->term_id);
  }

  /**
   * @param $name
   * @param $value
   *
   * @return bool|int|WP_Error
   */
  public function update(string $name, $value)
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
  public function parents()
  {
    return get_ancestors($this->term_id, $this->taxonomy, 'taxonomy');
  }

  /**
   * @param array $data
   * @param bool $error
   * @return int|WP_Error
   */
  public static function create(array $data)
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