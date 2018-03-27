<?php

namespace wpf\helpers;

/**
 * Class Icon
 *
 * @method static $this string fa(string $name, array $options)
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
            'gi' => 'glyphicon glyphicon-',
            'ui' => 'icon ',
            'wp' => 'dashicons dashicons-',
            'mi' => 'material-icons'
        ];

    /**
     * @param string $family
     * @param string $name
     * @param array $options
     * @return mixed
     */
    public static function familyIcon(string $family, string $name, array $options = [])
    {
        $class = self::getPrefix($family);
        // It's Material spike-nail :)
        if (!self::isMaterial($family)) {
            $class .= $name;
            $name = '';
        }
        Html::addCssClass($options, $class);

        return Html::tag(self::$tag_name, $name, $options);
    }

    /**
     * @param string $name
     * @return string
     */
    private static function getPrefix(string $name): string
    {
        return self::$prefixes[$name] ?? '';
    }

    /**
     * @param string $family
     * @return bool
     */
    private static function isMaterial(string $family): bool
    {
        return ($family == 'mi');
    }

    /**
     * @param string $family
     * @param array $options
     * @return mixed
     */
    public static function __callStatic(string $family, array $options)
    {
        // Add as first $family parameter to calling of familyIcon static method.
        array_unshift($options, $family);

        return call_user_func_array([self::class, 'familyIcon'], $options);
    }
}
