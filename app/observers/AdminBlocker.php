<?php

namespace app\observers;

use \wpf\app\Observer;
use \wpf\App;

/**
 * Class AdminBlocker
 * @package \app\observers
 */
class AdminBlocker
    extends Observer {

    /**
     * @param App $app
     */
    public function doUpdate( App $app ) {
        $update = function () use ( $app ) {
            if ( ! current_user_can('administrator') ) {
                header('HTTP/1.0 404 Not Found');
                exit();
            }
        };

        add_action('admin_menu', $update );
    }
}
