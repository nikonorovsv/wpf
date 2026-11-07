<?php

namespace wpf\app;

use ReflectionClass;
use ReflectionException;
use SplObserver;
use SplSubject;
use wpf\App;
use wpf\base\ConfigException;

/**
 * Class Observer
 * @package wpf\app
 */
abstract class Observer
    implements SplObserver
{
    private App $app;

    /**
     * AppObserver constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $app->attach($this);
    }

    /**
     * @param SplSubject $subject
     * @return void
     */
    public function update(SplSubject $subject): void
    {
        if ($subject === $this->app) {
            $this->doUpdate($subject);
        }
    }

    /**
     * @param App $app
     *
     * @return void
     */
    abstract public function doUpdate(App $app): void;

    /**
     * @param string $dir
     * @param string $name
     * @param array $options
     * @return string|string[]
     * @throws ConfigException
     * @throws ReflectionException
     */
    protected function getClassName(string $dir, string $name, array $options = []): string|array
    {
        $class = str_replace('/', '\\', "$dir/$name");
        $reflect = new ReflectionClass($class);
        if (!empty($options['subclass_of'])) {
            // ToDo: Check if method exists
            $base = call_user_func([$options['subclass_of'], 'getName']);
            if (!$reflect->isSubclassOf($base)) {
                throw new ConfigException(
                    __("Class '{$reflect->getName()}' must be inherited of '{$options['subclass_of']}' class.", 'wpf'));
            }
        }
        if (!empty($options['implements'])) {
            if (!$reflect->implementsInterface($options['implements'])) {
                throw new ConfigException(
                    __("Class '{$reflect->getName()}' must implement '{$options['implements']}' interface.", 'wpf'));
            }
        }

        return $class;
    }
}