<?php
namespace wpf\helpers;

/**
 * Class Html From yii-2
 * @package wpf\helpers
 */
class Html {
	/**
	 * @var array list of void elements (element name => 1)
	 * @see http://www.w3.org/TR/html-markup/syntax.html#void-element
	 */
	public static $voidElements
		= [
			'area'    => 1,
			'base'    => 1,
			'br'      => 1,
			'col'     => 1,
			'command' => 1,
			'embed'   => 1,
			'hr'      => 1,
			'img'     => 1,
			'input'   => 1,
			'keygen'  => 1,
			'link'    => 1,
			'meta'    => 1,
			'param'   => 1,
			'source'  => 1,
			'track'   => 1,
			'wbr'     => 1,
		];
	/**
	 * @var array the preferred order of attributes in a tag. This mainly affects the order of the attributes
	 * that are rendered by [[renderTagAttributes()]].
	 */
	public static $attributeOrder
		= [
			'type',
			'id',
			'class',
			'name',
			'value',
			'href',
			'src',
			'action',
			'method',
			'selected',
			'checked',
			'readonly',
			'disabled',
			'multiple',
			'size',
			'maxlength',
			'width',
			'height',
			'rows',
			'cols',
			'alt',
			'title',
			'rel',
			'media',
		];
	public static $dataAttributes
		= [
			'data',
			'data-ng',
			'ng',
			'aria'
		];
	public static $callStaticOrder
		= [
			'div',
			'span',
			'small',
			'abbr',
			'time',
			'strong',
			'em',
			'i',
			'p',
			'option',
			'li',
			'th',
			'tr',
			'td',
			'section',
			'footer',
			'header',
			'aside',
			'nav',
			'main',
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6'
		];
	
	/**
	 * Encodes special characters into HTML entities.
	 * The [[\yii\base\Application::charset|application charset]] will be used for encoding.
	 *
	 * @param string $content the content to be encoded
	 * @param boolean $doubleEncode whether to encode HTML entities in `$content`. If false,
	 * HTML entities in `$content` will not be further encoded.
	 *
	 * @return string the encoded content
	 * @see decode()
	 * @see http://www.php.net/manual/en/function.htmlspecialchars.php
	 */
	public static function encode( $content, $doubleEncode = TRUE ) {
		if ( htmlspecialchars_decode( $content ) == $content ) {
			return htmlspecialchars( $content, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8', $doubleEncode );
		}
		
		return $content;
	}
	
	/**
	 * Generates a complete HTML tag.
	 *
	 * @param string|boolean|null $name the tag name. If $name is `null` or `false`, the corresponding content will be
	 *     rendered without any tag.
	 * @param string $content the content to be enclosed between the start and end tags. It will not be HTML-encoded.
	 * If this is coming from end users, you should consider [[encode()]] it to prevent XSS attacks.
	 * @param array $options the HTML tag attributes (HTML options) in terms of name-value pairs.
	 * These will be rendered as the attributes of the resulting tag. The values will be HTML-encoded using
	 *     [[encode()]]. If a value is null, the corresponding attribute will not be rendered.
	 *
	 * For example when using `['class' => 'my-class', 'target' => '_blank', 'value' => null]` it will result in the
	 * html attributes rendered like this: `class="my-class" target="_blank"`.
	 *
	 * See [[renderTagAttributes()]] for details on how attributes are being rendered.
	 *
	 * @return string the generated HTML tag
	 * @see beginTag()
	 * @see endTag()
	 */
	public static function tag( $name, $content = '', $options = [] ) {
		if ( $name === NULL || $name === FALSE ) {
			return $content;
		}
		$html = "<$name" . static::renderTagAttributes( $options ) . '>';
		
		return isset( static::$voidElements[ strtolower( $name ) ] ) ? $html : "$html$content</$name>";
	}
	
	/**
	 * Renders the HTML tag attributes.
	 *
	 * Attributes whose values are of boolean type will be treated as
	 * [boolean attributes](http://www.w3.org/TR/html5/infrastructure.html#boolean-attributes).
	 *
	 * Attributes whose values are null will not be rendered.
	 *
	 * The values of attributes will be HTML-encoded using [[encode()]].
	 *
	 * The "data" attribute is specially handled when it is receiving an array value. In this case,
	 * the array will be "expanded" and a list data attributes will be rendered. For example,
	 * if `'data' => ['id' => 1, 'name' => 'yii']`, then this will be rendered:
	 * `data-id="1" data-name="yii"`.
	 * Additionally `'data' => ['params' => ['id' => 1, 'name' => 'yii'], 'status' => 'ok']` will be rendered as:
	 * `data-params='{"id":1,"name":"yii"}' data-status="ok"`.
	 *
	 * @param array $attributes attributes to be rendered. The attribute values will be HTML-encoded using [[encode()]].
	 *
	 * @return string the rendering result. If the attributes are not empty, they will be rendered
	 * into a string with a leading white space (so that it can be directly appended to the tag name
	 * in a tag. If there is no attribute, an empty string will be returned.
	 */
	public static function renderTagAttributes( $attributes ) {
		if ( count( $attributes ) > 1) {
			$sorted = [];
			foreach ( static::$attributeOrder as $name ) {
				if ( isset( $attributes[ $name ] ) ) {
					$sorted[ $name ] = $attributes[ $name ];
				}
			}
			$attributes = array_merge( $sorted, $attributes );
		}
		$html = '';
		foreach ( $attributes as $name => $value ) {
			if ( is_bool( $value ) ) {
				if ( $value ) {
					$html .= " $name";
				}
			} elseif ( is_array( $value ) ) {
				if ( in_array( $name, static::$dataAttributes ) ) {
					foreach ( $value as $n => $v ) {
						if ( is_array( $v ) ) {
							$html .= " $name-$n='" . json_encode( $v ) . "'";
						} else {
							$html .= " $name-$n=\"" . static::encode( $v ) . '"';
						}
					}
				} elseif ( $name === 'class') {
					if ( empty( $value ) ) {
						continue;
					}
					$html .= " $name=\"" . static::encode( implode( ' ', $value ) ) . '"';
				} elseif ( $name === 'style') {
					if ( empty( $value ) ) {
						continue;
					}
					$html .= " $name=\"" . static::encode( static::cssStyleFromArray( $value ) ) . '"';
				} else {
					$html .= " $name='" . json_encode( $value ) . "'";
				}
			} elseif ( $value !== NULL ) {
				// WP: Не работает ссылка редактирования, если заменить & на &amp;
				$html .= " $name=\"" . static::encode( $value ) . '"';
			}
		}
		
		return $html;
	}
	
	/**
	 * Converts a CSS style array into a string representation.
	 *
	 * For example,
	 *
	 * ```php
	 * print_r(Html::cssStyleFromArray(['width' => '100px', 'height' => '200px']));
	 * // will display: 'width: 100px; height: 200px;'
	 * ```
	 *
	 * @param array $style the CSS style array. The array keys are the CSS property names,
	 * and the array values are the corresponding CSS property values.
	 *
	 * @return string the CSS style string. If the CSS style is empty, a null will be returned.
	 */
	public static function cssStyleFromArray( array $style ) {
		$result = '';
		foreach ( $style as $name => $value ) {
			$result .= "$name: $value; ";
		}
		
		// return null if empty to avoid rendering the "style" attribute
		return $result === '' ? NULL : rtrim( $result );
	}
	
	/**
	 * Adds a CSS class (or several classes) to the specified options.
	 * If the CSS class is already in the options, it will not be added again.
	 * If class specification at given options is an array, and some class placed there with the named (string) key,
	 * overriding of such key will have no effect. For example:
	 *
	 * ```php
	 * $options = ['class' => ['persistent' => 'initial']];
	 * Html::addCssClass($options, ['persistent' => 'override']);
	 * var_dump($options['class']); // outputs: array('persistent' => 'initial');
	 * ```
	 *
	 * @param array $options the options to be modified.
	 * @param string|array $class the CSS class(es) to be added
	 */
	public static function addCssClass( &$options, $class ) {
		if ( isset( $options['class'] ) ) {
			if ( is_array( $options['class'] ) ) {
				$options['class'] = self::mergeCssClasses( $options['class'], (array) $class );
			} else {
				$classes            = preg_split('/\s+/', $options['class'], - 1, PREG_SPLIT_NO_EMPTY );
				$options['class'] = implode( ' ', self::mergeCssClasses( $classes, (array) $class ) );
			}
		} else {
			$options['class'] = $class;
		}
	}
	
	/**
	 * Merges already existing CSS classes with new one.
	 * This method provides the priority for named existing classes over additional.
	 *
	 * @param array $existingClasses already existing CSS classes.
	 * @param array $additionalClasses CSS classes to be added.
	 *
	 * @return array merge result.
	 */
	private static function mergeCssClasses( array $existingClasses, array $additionalClasses ) {
		foreach ( $additionalClasses as $key => $class ) {
			if ( is_int( $key ) && ! in_array( $class, $existingClasses ) ) {
				$existingClasses[] = $class;
			} elseif ( ! isset( $existingClasses[ $key ] ) ) {
				$existingClasses[ $key ] = $class;
			}
		}
		
		return array_unique( $existingClasses );
	}
	
	/**
	 * Removes a CSS class from the specified options.
	 *
	 * @param array $options the options to be modified.
	 * @param string|array $class the CSS class(es) to be removed
	 */
	public static function removeCssClass( &$options, $class ) {
		if ( isset( $options['class'] ) ) {
			if ( is_array( $options['class'] ) ) {
				$classes = array_diff( $options['class'], (array) $class );
				if ( empty( $classes ) ) {
					unset( $options['class'] );
				} else {
					$options['class'] = $classes;
				}
			} else {
				$classes = preg_split('/\s+/', $options['class'], - 1, PREG_SPLIT_NO_EMPTY );
				$classes = array_diff( $classes, (array) $class );
				if ( empty( $classes ) ) {
					unset( $options['class'] );
				} else {
					$options['class'] = implode(' ', $classes );
				}
			}
		}
	}

	/**
	 * @param $options
	 * @param $class
	 *
	 * @return bool
	 */
	public static function hasCssClass( $options, $class ) {
		if ( isset( $options['class'] ) ) {
			if ( is_array( $options['class'] ) ) {
				if ( in_array( $class, $options['class'] ) ) {
					return TRUE;
				}
			} elseif ( strpos( $options['class'], $class ) ) {
				return TRUE;
			}
		}

		return FALSE;
	}
	
	/**
	 * @param $name
	 * @param $args
	 *
	 * @return string
	 */
	public static function __callStatic( $name, $args ) {
		if ( in_array( $name, self::$callStaticOrder ) ) {
			return self::tag( $name, ...$args );
		}
		
		return $args[0];
	}
	
	/**
	 * @param string $text
	 * @param null $url
	 * @param array $options
	 *
	 * @return string
	 */
	public static function a( $text, $url = NULL, $options = [] ) {
		if ( ! is_null( $url ) ) {
			$options['href'] = $url;
		}
		
		return static::tag('a', $text, $options );
	}

	/**
	 * @param string $text
	 * @param string $email
	 * @param array $options
	 *
	 * @return string
	 */
	public static function mailto( $text, $email, $options = [] ) {
		return static::a( $text, "mailto: {$email}", $options );
	}
	
	/**
	 * @param string $src
	 * @param array $options
	 *
	 * @return string
	 */
	public static function img( $src = NULL, $options = [] ) {
		if ( ! is_null( $src ) ) {
			$options['src'] = $src;
		}
		
		return static::tag('img', NULL, $options );
	}

	/**
	 * @param array $items
	 * @param array $options
	 *
	 * @return string
	 */
	public static function ul( $items, $options = [] ) {
		$tag         = ArrayHelper::remove( $options, 'tag', 'ul');
		$encode      = ArrayHelper::remove( $options, 'encode', FALSE );
		$formatter   = ArrayHelper::remove( $options, 'item');
		$separator   = ArrayHelper::remove( $options, 'separator', "\n");
		$itemOptions = ArrayHelper::remove( $options, 'itemOptions', []);
		$classFor    = ArrayHelper::remove( $itemOptions, 'class_for', null );

		if ( empty( $items ) ) {
			return static::tag( $tag, '', $options );
		}
		$results = [];
		foreach ( $items as $i => $item ) {
			if ( $formatter !== NULL ) {
				$results[] = call_user_func( $formatter, $item, $i );
			} else {
				$_itemOptions = $itemOptions;
				if ( isset( $classFor ) && array_key_exists( $i, $classFor ) ) {
					static::addCssClass( $_itemOptions, $classFor[ $i ] );
				}
				$results[] = static::tag('li', $encode ? static::encode( $item ) : $item, $_itemOptions );
			}
		}

		return static::tag( $tag, $separator . join( $separator, $results ) . $separator, $options );
	}
	
	/**
	 * @param $items
	 * @param array $options
	 *
	 * @return string
	 */
	public static function ol( $items, $options = [] ) {
		$options['tag'] = 'ol';
		
		return static::ul( $items, $options );
	}

	/**
	 * @param $items
	 * @param array $options
	 *
	 * @return string
	 */
	public static function select( $items, $options = [] ) {
		$options['tag'] = 'select';
		$options['item'] = function ( $item, $index ) {
			$itemOptions = [];
			if ( is_array( $item ) ) {
				$label = $item['label'];
                $itemOptions['value'] = $item['value'];
				if ( ! empty( $item['options'] ) && ArrayHelper::isAssociative( $item['options'] ) ) {
                    $itemOptions = array_merge( $itemOptions, $item['options'] );
                }
			} else {
				$label = $item;
				$itemOptions['value'] = $item;
			}

			return static::tag('option', $label, $itemOptions );
		};

		return static::ul( $items, $options );
	}
}
