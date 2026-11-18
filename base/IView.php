<?php

namespace wpf\base;

defined('ABSPATH') || exit;

/**
 * Interface IView
 * @package wpf\base
 */
interface IView
{

    /**
     * @param $tpl
     * @param array $vars
     *
     * @return mixed
     */
    public function render($tpl, array $vars = []): string;
}