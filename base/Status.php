<?php

namespace wpf\base;

/**
 * Class Status
 * @package wpf\wp
 */
abstract class Status implements IStatus
{
  abstract public function register(): void;
}