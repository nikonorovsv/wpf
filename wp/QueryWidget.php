<?php

namespace wpf\wp;

use WP_Query;
use wpf\base\Widget;

/**
 * Class QueryWidget
 * @package app\widgets
 */
class QueryWidget extends Widget
{
  use QueryBuilder;

  protected WP_Query $query;

  /**
   * QueryWidget constructor.
   *
   * @param array $conf
   */
  public function __construct(array $conf = [])
  {
    parent::__construct($conf);

    $this->query = $this->query();
  }
}