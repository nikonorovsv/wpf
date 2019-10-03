<?php

namespace wpf\app\observers;

use wpf\App;
use wpf\app\Observer;
use wpf\helpers\WP;

/**
 * Class AssetsDefiner.
 *
 * @package wpf\app\observers
 */
class AssetsDefiner extends Observer
{
    /**
     * @param App $app
     */
    public function doUpdate(App $app)
    {
        $update = function () use ($app) {
            // Deregister custom styles
            if ($app->deregister_styles) {
                foreach ($app->deregister_styles as $style) {
                    wp_deregister_style($style);
                }
            }
            // Register and Set styles
            if ($app->register_styles) {
                $enqueue_styles = [];
                foreach ($app->register_styles as $style) {
                    if (is_string($style)) {
                        $enqueue_styles[] = $style;
                        continue;
                    }
                    if (!$style['cdn']) {
                        $style['src'] = WP::uri($style['src']);
                    }
                    wp_register_style($style['name'], $style['src'],
            $style['deps'] ?? [],
            $style['ver'] ?? false,
            $style['media'] ?? 'all'
          );
                    if ($style['enqueue']) {
                        $enqueue_styles[] = $style['name'];
                    }
                }
                array_map(function ($item) {
                    wp_enqueue_style($item);
                }, $enqueue_styles);
            }
            // Deregister custom scripts
            if ($app->deregister_scripts) {
                foreach ($app->deregister_scripts as $script) {
                    wp_deregister_script($script);
                }
            }
            // Register and Set scripts
            if ($app->register_scripts) {
                $enqueue_scripts = [];
                foreach ($app->register_scripts as $script) {
                    if (is_string($script)) {
                        $enqueue_scripts[] = ['name' => $script];
                        continue;
                    }
                    if (!$script['cdn']) {
                        $script['src'] = WP::uri($script['src']);
                    }
                    wp_register_script($script['name'], $script['src'],
            $script['deps'] ?? [],
            $script['ver'] ?? false,
            $script['in_footer'] ?? false
          );
                    if ($script['enqueue']) {
                        $enqueue_scripts[] = $script;
                    }
                }

                // Enqueue scripts
                array_map(function ($item) {
                    wp_enqueue_script($item['name']);
                    if (!empty($item['localize'])) {
                        if (defined('THEME_NONCE_KEY')) {
                            $item['localize']['nonce'] = wp_create_nonce(THEME_NONCE_KEY);
                        }
                        wp_localize_script($item['name'], 'WPData', $item['localize']);
                    }
                }, $enqueue_scripts);
            }
        };
        add_action('wp_enqueue_scripts', $update);
    }
}
