<?php

namespace wpf\app;

use \SplObserver;
use \SplSubject;
use \wpf\App;

/**
 * Class Observer
 * @package wpf\app
 */
abstract class Observer
	implements SplObserver {
	private $app;
	
	/**
	 * AppObserver constructor.
	 *
	 * @param App $app
	 */
	public function __construct( App $app ) {
		$this->app = $app;
		$app->attach( $this );
	}
	
	/**
	 * @param SplSubject $subject
	 */
	public function update( SplSubject $subject ) {
		if ( $subject === $this->app ) {
			$this->doUpdate( $subject );
		}
	}
	
	/**
	 * @param App $app
	 *
	 * @return mixed
	 */
	abstract public function doUpdate( App $app );
}