# WPF
WPF is a WordPress Framework to make your creating of theme easy.

## Requires
* PHP v7.*
* Wordpress
* Advanced Custom Fields (ACF) Wordpress Plugin

## Quick start
1. [Install Composer](https://getcomposer.org/download/).
2. Move to the theme folder: `cd {WP_path}/wp-config/themes/{theme_name}`
3. Create **composer.json** file and add WPF requires like this:
    ```json
    {
        "require": {
            "nikonorovsv/wpf": "dev-master"
        }
    }
    ```
4. Run `composer install`
5. Create your **app.config.json** into theme base directory.
6. Open your **functions.php** and create your App:
    ```php
    get_template_part( 'vendor/autoload' );
    get_template_part( 'vendor/nikonorovsv/wpf/functions' );
 
    $conf = [
    	'vendor/nikonorovsv/wpf/wpf.config.json',
    	'app.config.json'
    ];
    
    $app = app();
    $app->loadAttributes( $app::readJsonConf( $conf ) );
    $app->applyObservers();
    ```
    
## Observers
Observers is middleware classes to execute your own code before template will be loaded. You can specify your own observers as child for `\wpf\app\Observer` class. Each of observers needed must to be include in your `app.config.json` file into `observers` option. Also you can use the observers included in the framework. They are located in the folder `/app/observers`. See example:
```json
{
    "observers": [
        "\\wpf\\app\\observers\\OptionsPagesDefiner",
        "\\wpf\\app\\observers\\LocalFieldGroupDefiner",
        "\\wpf\\app\\observers\\ConstantsDefiner",
        "\\wpf\\app\\observers\\EntityDefiner",
        "\\wpf\\app\\observers\\ThemeSupportsDefiner",
        "\\wpf\\app\\observers\\ImageSizesDefiner",
        "\\wpf\\app\\observers\\MenuDefiner",
        "\\wpf\\app\\observers\\ActionRemover",
        "\\wpf\\app\\observers\\AllowCORS",
        "\\wpf\\app\\observers\\PostStatusDefiner",
        "\\app\\observers\\QuerySetter"
      ],
}
```
It's convenient to create observer classes, as in the following example:
```php
namespace app\observers;

use \wpf\app\Observer;
use \wpf\App;
use \WP_Query;

/**
 * Class QuerySetter
 * @package \app\observers
 */
class QuerySetter
    extends Observer {

    /**
     * @param App $app
     */
    public function doUpdate( App $app ) {

        /**
         * @param WP_Query $query
         *
         * @return bool
         */
        $update = function ( WP_Query $query ) use ( $app ) {
            if ( is_admin() || ! $query->is_main_query() ) {
                return FALSE;
            }

            // Your rules...
        };

        add_action( 'pre_get_posts', $update );
    }
}
```

## Widgets
All parts in the VFF that you want to display on the page are recommended to be formatted as widgets. A widget is an object of a class inherited from the `\wpf\base\Widget` class. Widget classes should be located in the `/app/widgets` folder. Widgets can generate their own html, or use templates. You can override `$this->render()` method to return html without using of templete. Let's look at the code.
```php
namespace app\widgets;

use \wpf\base\Widget;
use \wpf\helpers\Html;
use \wpf\helpers\Icon;

/**
 * Class SomeWidget
 * @package app\widgets
 */
class SomeWidget extends Widget {

    public $template = 'widget';

    /**
     * SomeWidget constructor.
     *
     * @param array $conf
     */
    public function __construct( array $conf = [] ) {
        parent::__construct( $conf );

        $this->title = 'Some title';
        $this->content = 'Some text';
    }

    /**
     * @return string
     */   
    public function render():string {
        return parent::render();
    }
}
```

Templates should be located in `/app/views` folder and look like this:
```php
use \wpf\helpers\Html;

// Declare defaults. They will be replace with values defined by widget.
extract( [
    'title'           => '',
    'content'         => '',
    'title_options'   => [],
    'content_options' => [],
    'options'         => []
], EXTR_SKIP );

Html::addCssClass( $options, 'widget');

?>

<div <?= Html::renderTagAttributes( $options ) ?>>
    <?php
    if ( $title ) {
        Html::addCssClass( $title_options, 'title');
        echo Html::div( $title, $title_options );
    }
    Html::addCssClass( $content_options, 'content');
    echo Html::div( $content, $content_options ); ?>
</div>
```
    
Widgets can use `\wpf\wp\QueryBuilder` trait for easy way to include some query to DB.
```php
namespace app\widgets;

use \wpf\base\Widget;
use \wpf\wp\QueryBuilder;

/**
 * Class SomeWidget
 * @package app\widgets
 */
class SomeWidget extends Widget {

    use QueryBuilder;

    public $template = 'widget';

    /**
     * SomeWidget constructor.
     *
     * @param array $conf
     */
    public function __construct( array $conf = [] ) {
        parent::__construct( $conf );

        $this->items = $this->query()->posts;
    }

    /**
     * @return array
     */
    public function queryArgs():array {
        return [
            'post_type'      => 'custom_post_type',
            'post_status'    => 'publish',
            'posts_per_page' => 10
        ];
    }
}
```

## About
Hey! Do you have any ideas how to do WPF better? 
[Send me a letter](mailto:nikonorovsv@gmail.com).
  
    
