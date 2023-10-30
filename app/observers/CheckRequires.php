<?php

namespace wpf\app\observers;

use wpf\app\Observer;
use wpf\App;
use wpf\base\ConfigException;

/**
 * Class CheckRequires
 * @package wpf\app\observers
 */
class CheckRequires
  extends Observer
{
  /**
   * @param App $app
   *
   * @return bool
   * @throws ConfigException
   */
  public function doUpdate(App $app)
  {
    // Require ACF
    if (!is_plugin_active('advanced-custom-fields-pro/acf.php') and current_user_can('activate_plugins')) {
      // Stop activation redirect and show error
      throw new ConfigException(
        __('Sorry, but this plugin requires the "ACF Pro" to be installed and active.', 'wpf'));
    }
    // PHP_VERSION_ID is available as of PHP 5.2.7, if our
    // version is lower than that, then emulate it
    if (!defined('PHP_VERSION_ID')) {
      $version = explode('.', PHP_VERSION);
      define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
    }
    if (PHP_VERSION_ID < 70000) {
      throw new ConfigException(
        __('Sorry, WPF framework supports PHP after 7.0 version.', 'wpf'));
    }
  }
}