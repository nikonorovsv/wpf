<?php

namespace wpf\app\observers;

use wpf\app\Observer;
use wpf\App;

/**
 * Class GoogleMapsDefiner
 * @package wpf\app\observers
 */
class GoogleMapsDefiner
  extends Observer
{

  /**
   * @param App $app
   */
  public function doUpdate(App $app)
  {

    /**
     * Filter add API key for Google Map
     * @param $api
     *
     * @return mixed
     */
//		add_filter('acf/fields/google_map/api', function ( $api ) {
//			$api['key'] = GOOGLE_MAPS_API_KEY;
//
//			return $api;
//		} );

    /**
     * Add Google Maps API key
     */
    add_action('acf/init', function () {
      acf_update_setting('google_api_key', GOOGLE_MAPS_API_KEY);
    });
  }
}