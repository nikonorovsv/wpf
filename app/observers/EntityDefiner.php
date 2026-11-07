<?php

namespace wpf\app\observers;

use InvalidArgumentException;
use ReflectionException;
use wpf\App;
use wpf\app\Observer;
use wpf\base\IEntity;
use wpf\base\ConfigException;
use wpf\base\FileNotFoundException;
use wpf\helpers\WP;

/**
 * Class EntityDefiner
 * @package wpf\app\observers
 */
class EntityDefiner
    extends Observer
{

    const BASE_ENTITY = IEntity::class;

    /**
     * @param App $app
     * @return void
     * @throws ConfigException
     * @throws FileNotFoundException
     * @throws ReflectionException
     */
    public function doUpdate(App $app): void
    {
        if (!$app->entities) {
            return;
        } elseif (!$app->entities_dir) {
            throw new InvalidArgumentException(
                __("Parameter 'entities_dir' must have been defined in '/wpf/wpf.config.json' file.", 'wpf'));
        } elseif (!is_dir(WP::path($app->entities_dir))) {
            throw new FileNotFoundException(
                __("Parameter 'entities_dir' in '/wpf/wpf.config.json' file must be correct path to folder.", 'wpf'));
        }

        $update = function () use ($app) {
            foreach ($app->entities as $entity) {
                $class = $this->getClassName($app->entities_dir, $entity, [
                    'implements' => self::BASE_ENTITY
                ]);
                (new $class)->register();
            }
        };

        add_action('init', $update);
    }
}
