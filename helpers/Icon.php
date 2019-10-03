<?php

namespace wpf\helpers;

/**
 * Class Icon.
 *
 * @method static $this string fa(string $name, array $options)
 *
 * @package wpf\helpers
 */
class Icon
{
    /**
     * @var string
     */
    public static $tag_name = 'i';
    /**
     * @var array
     */
    public static $prefixes
    = [
      'fa' => 'fa fa-',
      'fas' => 'fas fa-',
      'far' => 'far fa-',
      'fal' => 'fal fa-',
      'fab' => 'fab fa-',
      'gi' => 'glyphicon glyphicon-',
      'ui' => 'icon ',
      'wp' => 'dashicons dashicons-',
      'md' => 'material-icons',
      'uk' => 'icon: ',
    ];

    /**
     * @param string $family
     * @param string $name
     * @param array  $options
     *
     * @return mixed
     */
    public static function familyIcon(string $family, string $name, array $options = []): string
    {
        $class = self::getPrefix($family);
        switch ($family) {
      case 'md':
        break;
      case 'uk':
        $options['uk-icon'] = $class . $name;
        $name = '';
        break;
      case 'fa':
      case 'gi':
      case 'ui':
      case 'wp':
      default:
        Html::addCssClass($options, $class . $name);
        $name = '';
        break;
    }

        return Html::tag(self::$tag_name, $name, $options);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private static function getPrefix(string $name): string
    {
        return self::$prefixes[$name] ?? '';
    }

    /**
     * @param string $family
     * @param array  $options
     *
     * @return mixed
     */
    public static function __callStatic(string $family, array $options)
    {
        // Add as first $family parameter to calling of familyIcon static method.
        array_unshift($options, $family);

        return call_user_func_array([self::class, 'familyIcon'], $options);
    }
}
