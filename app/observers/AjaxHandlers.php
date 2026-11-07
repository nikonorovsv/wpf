<?php

namespace wpf\app\observers;

use wpf\App;
use wpf\app\Observer;
use wpf\base\FileNotFoundException;
use wpf\base\ConfigException;
use wpf\base\AjaxHandler;
use wpf\helpers\WP;
use InvalidArgumentException;
use ReflectionException;

/**
 * Class AjaxHandlers
 * @package wpf\app\observers
 */
class AjaxHandlers
    extends Observer
{

    const BASE_HANDLER = AjaxHandler::class;

    /**
     * @param App $app
     * @return void
     * @throws FileNotFoundException
     * @throws ReflectionException
     * @throws ConfigException
     */
    public function doUpdate(App $app): void
    {
        if (!wp_doing_ajax()) {
            return;
        } elseif (!$app->ajax_handlers) {
            return;
        } elseif (!is_array($app->ajax_handlers)) {
            throw new InvalidArgumentException(
                __("The value of 'ajax_handlers' parameter must be array.", 'wpf'));
        } elseif (!$app->ajax_handlers_dir) {
            throw new InvalidArgumentException(
                __("Parameter 'ajax_handlers_dir' must have been defined in '/wpf/wpf.config.json' file.", 'wpf'));
        } elseif (!is_dir(WP::path($app->ajax_handlers_dir))) {
            throw new FileNotFoundException(
                __("Parameter 'ajax_handlers_dir' in '/wpf/wpf.config.json' file must be correct path to folder.", 'wpf'));
        }

        $this->registerHandlers($app);
    }

    /**
     * @param App $app
     * @return void
     * @throws ConfigException
     * @throws ReflectionException
     */
    public function registerHandlers(App $app): void
    {
        foreach ($app->ajax_handlers as $handler) {
            $class = $this->getClassName($app->ajax_handlers_dir, $handler, [
                'subclass_of' => self::BASE_HANDLER
            ]);
            $action = $class::ACTION_NAME;
            add_action("wp_ajax_{$action}", [$class, 'run']);
            add_action("wp_ajax_nopriv_{$action}", [$class, 'run']);
        }
    }
}