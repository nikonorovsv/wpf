<?php

namespace wpf\helpers;

/**
 * Class Debug
 * @package wpf\helpers
 */
class Debug
{
    /**
     * @return bool
     */
    public static function isDebug(): bool
    {
        return defined('WP_DEBUG') && WP_DEBUG;
    }

    /**
     * @param array ...$object
     *
     * @return void
     */
    public static function varDump(...$object): void
    {
        if (!self::isDebug()) {
            return;
        }
        if (!is_user_logged_in()) {
            return;
        }
        echo '<pre>';
        var_dump(...$object);
        echo '</pre>';
    }

    /**
     * @param $object
     *
     * @return void
     */
    public static function varExport($object): void
    {
        if (!self::isDebug()) {
            return;
        }
        if (!is_user_logged_in()) {
            return;
        }
        echo '<pre>';
        var_export($object);
        echo '</pre>';
    }

    /**
     * @param string $msg
     * @param string $title
     * @param string $file
     * @return void
     */
    public static function logIt(string $msg, string $title, string $file): void
    {
        if (!self::isDebug()) {
            return;
        }
        error_log(join(' | ', [
            current_time('d.m.Y h:i:s'),
            $title,
            print_r($msg, TRUE) . "\n"
        ]), 3, WP::path($file));
    }
}