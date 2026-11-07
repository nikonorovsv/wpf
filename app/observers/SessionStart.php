<?php

namespace wpf\app\observers;

use wpf\app\Observer;
use wpf\App;

/**
 * Class SessionStart
 * @package wpf\app\observers
 */
class SessionStart
    extends Observer
{
    /**
     * @param App $app
     * @return void
     */
    public function doUpdate(App $app): void
    {
        $update = function () {
            if (!session_id()) {
                session_start();
            }
        };

        add_action('init', $update);
    }
}