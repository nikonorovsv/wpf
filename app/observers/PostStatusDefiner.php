<?php
namespace wpf\app\observers;

use \wpf\App;
use \wpf\app\Observer;
use \wpf\base\ConfigException;
use \wpf\helpers\ArrayHelper;
use \wpf\helpers\WP;
use \InvalidArgumentException;

/**
 * Class PostStatusDefiner
 * @package \wpf\app\observers
 */
class PostStatusDefiner
    extends Observer {

    /**
     * @param App $app
     * @return bool
     */
    public function doUpdate( App $app ) {
        if ( ! $app->post_statuses ) {
            return FALSE;
        } elseif ( ! ArrayHelper::isAssociative( $app->post_statuses ) ) {
            throw new InvalidArgumentException( __("Parameter 'post_statuses' must be an object.", 'wpf') );
        }
        // Register all post statuses
        foreach ( $app->post_statuese as $post_status => $args ) {
            register_post_status( $post_status, $args );
        }
    }
}
