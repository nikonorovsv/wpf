<?php

namespace wpf\app\observers;

use wpf\App;
use wpf\app\Observer;

/**
 * Class HideAdminBar
 * @package wpf\app\observers
 */
class HideAdminBar
    extends Observer
{
    /**
     * @param App $app
     * @return void
     */
    public function doUpdate(App $app): void
    {
        add_filter('show_admin_bar', '__return_false');
    }
}
