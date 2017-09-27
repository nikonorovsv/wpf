<?php
namespace wpf\base;

/**
 * Class Request
 * @package wpf\base
 */
class Request extends Component
{
	/**
	 * Request constructor.
	 *
	 * @param array $conf
	 */
	public function __construct( array $conf = [] ) {
		$conf = wp_parse_args( $conf, $_REQUEST );

		parent::__construct( $conf );
	}
}