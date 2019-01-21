<?php
namespace wpf\app\observers;

use \ReflectionClass;
use \InvalidArgumentException;
use \wpf\App;
use \wpf\app\Observer;
use \wpf\base\ConfigException;
use \wpf\helpers\WP;

/**
 * Class PostStatusDefiner
 * @package \wpf\app\observers
 */
class PostStatusDefiner
    extends Observer {

    /**
     * @param App $app
     * @return mixed|void
     */
    public function doUpdate( App $app ) {
        if ( ! $app->statuses ) {
            return;
        } elseif ( ! $app->statuses_dir ) {
            throw new InvalidArgumentException(
                __("Parameter 'statuses_dir' must have been defined in '/wpf/wpf.config.json' file.", 'wpf') );
        } elseif ( ! is_dir( WP::path( $app->statuses_dir ) ) ) {
            throw new FileNotFoundException(
                __("Parameter 'statuses_dir' in '/wpf/wpf.config.json' file must be correct path to folder.", 'wpf') );
        }

        $update = function () use ( $app ) {
            foreach ( $app->statuses as $status ) {
                $class   = str_replace('/', '\\', "{$app->statuses_dir}/{$status}");
                $reflect = new ReflectionClass( $class );
                if ( ! $reflect->implementsInterface('\wpf\base\IStatus') ) {
                    throw new ConfigException(
                        __("Class '{$reflect->getName()}' must implement IStatus interface.", 'wpf') );
                }
                $status = new $class();
                $status->register();
            }
        };

        add_action('init', $update );
    }
}
