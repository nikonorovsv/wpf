<?php

namespace wpf\helpers;

/**
 * Class IfTTT
 * @package wpf\helpers
 */
class IfTTT
{
  /**
   * @param callable $ask
   * @param callable $question
   */
  public static function run(callable $ask, callable $question)
  {
    if (call_user_func($ask)) {
      call_user_func($question);
    }
  }

  /**
   * @param array $faq
   */
  public static function runArray(array $faq = [])
  {
    if (ArrayHelper::isAssociative($faq)) {
      foreach ($faq as $ask => $question) {
        self::run($ask, $question);
      }
    } elseif (ArrayHelper::isIndexed($faq)) {
      foreach ($faq as $aq) {
        if (is_array($aq)) {
          self::run($aq[0], $aq[1]);
        }
      }
    }
  }
}