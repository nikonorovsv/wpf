<?php
namespace wpf\wp;

use \wpf\base\Widget;
use \wpf\base\Request;

/**
 * Class QueryWidget
 * @package app\widgets
 */
class QueryWidget extends Widget
{
	use QueryBuilder;

	protected $request, $query;

    /**
	 * QueryWidget constructor.
	 *
	 * @param array $conf
	 */
    public function __construct( array $conf = [] ) {
        parent::__construct( $conf );

        $this->request = new Request();
	    $this->query   = $this->query();
    }
}