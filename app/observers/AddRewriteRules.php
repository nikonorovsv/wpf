<?php

namespace wpf\app\observers;

use wpf\App;
use wpf\app\Observer;
use wpf\base\ConfigException;
use wpf\helpers\ArrayHelper;

/**
 * Class AddRewriteRules.
 *
 * @package wpf\app\observers
 */
class AddRewriteRules extends Observer
{
    /**
     * @param App $app
     *
     * @return bool
     *
     * @throws ConfigException
     */
    public function doUpdate(App $app)
    {
        if (!$app->rewrite_rules) {
            return false;
        }

        $update = function () use ($app) {
            global $wp_rewrite;
            foreach ($app->rewrite_rules as $regexp => $matches) {
                $i = (int) ArrayHelper::remove($matches, 'matches_position', 1);
                $query_string = ArrayHelper::remove($matches, 'query_string', 'index.php?');
                $priority = ArrayHelper::remove($matches, 'priority', 'top');
                if (!in_array($priority, ['top', 'bottom'])) {
                    $priority = 'top';
                }

                add_filter('query_vars', function ($vars) use ($matches) {
                    return array_merge($vars, array_keys($matches));
                });

                $_regexp = [$regexp];
                $_query = [];
                foreach ($matches as $name => $match) {
                    $_regexp[] = $match;
                    $_query[] = $name . '=$matches[' . $i . ']';
                    ++$i;
                }

                $regexp = join('/', $_regexp) . '/?$';
                $query = $query_string . join('&', $_query);

                add_rewrite_rule($regexp, $query, $priority);
            }
            $wp_rewrite->flush_rules();
        };

        add_action('init', $update);
    }
}
