<?php

namespace wpf\app\observers;

use wpf\app\Observer;
use wpf\App;
use wpf\base\ConfigException;

/**
 * Class ActionRemover.
 *
 * @package wpf\app\observers
 */
class ActionRemover extends Observer
{
    /**
     * @param App $app
     *
     * @throws ConfigException
     */
    public function doUpdate(App $app)
    {
        $update = function () use ($app) {
            if (!$app->remove_actions) {
                return false;
            }
            foreach ($app->remove_actions as $args) {
                if (!is_array($args)) {
                    throw new ConfigException(
            __("All elements of 'remove_actions' property in '*.config.json' must be arrays too.", 'wpf'));
                }
                remove_action(...$args);
            }
        };
        add_action('after_setup_theme', $update);
    }
}
