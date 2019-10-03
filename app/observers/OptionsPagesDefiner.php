<?php

namespace wpf\app\observers;

use wpf\App;
use wpf\app\Observer;
use wpf\base\ConfigException;

/**
 * Class OptionsPagesDefiner.
 *
 * @package wpf\app\observers
 */
class OptionsPagesDefiner extends Observer
{
    /**
     * @param App $app
     *
     * @return bool
     *
     * @throws ConfigException
     */
    public function doUpdate(App $app)
    {
        if (!$app->options_pages) {
            return false;
        } elseif (!function_exists('acf_add_options_page')) {
            return false;
        }
        foreach ($app->options_pages as $args) {
            if (!is_array($args)) {
                throw new ConfigException(__("All elements of 'options_pages' array in '*.config.json' must be objects.", 'wpf'));
            }
            acf_add_options_page($args);
        }
    }
}
