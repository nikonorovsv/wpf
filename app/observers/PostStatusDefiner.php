<?php

namespace wpf\app\observers;

use InvalidArgumentException;
use ReflectionException;
use wpf\App;
use wpf\app\Observer;
use wpf\base\ConfigException;
use wpf\base\FileNotFoundException;
use wpf\helpers\WP;

/**
 * Class PostStatusDefiner
 * @package wpf\app\observers
 */
class PostStatusDefiner
    extends Observer
{

    const BASE_STATUS = '\wpf\base\IStatus';

    /**
     * @param App $app
     * @return void
     * @throws ConfigException
     * @throws FileNotFoundException
     * @throws ReflectionException
     */
    public function doUpdate(App $app): void
    {
        if (!$app->statuses) {
            return;
        } elseif (!$app->statuses_dir) {
            throw new InvalidArgumentException(
                __("Parameter 'statuses_dir' must have been defined in '/wpf/wpf.config.json' file.", 'wpf'));
        } elseif (!is_dir(WP::path($app->statuses_dir))) {
            throw new FileNotFoundException(
                __("Parameter 'statuses_dir' in '/wpf/wpf.config.json' file must be correct path to folder.", 'wpf'));
        }

        $update = function () use ($app) {
            foreach ($app->statuses as $status) {
                $class = $this->getClassName($app->statuses_dir, $status, [
                    'implements' => self::BASE_STATUS
                ]);
                (new $class)->register();
            }
        };

        add_action('init', $update);
    }
}
