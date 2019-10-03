<?php

namespace wpf\app\observers;

use wpf\app\Observer;
use wpf\App;

/**
 * Class RemoveArchiveTitlePrefix.
 *
 * @package wpf\app\observers
 */
class RemoveArchiveTitlePrefix extends Observer
{
    /**
     * @param App $app
     *
     * @return bool
     */
    public function doUpdate(App $app)
    {
        add_filter('get_the_archive_title', function ($title) {
            return preg_replace('~^[^:]+: ~', '', $title);
        });
    }
}
