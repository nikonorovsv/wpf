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
      "autoload": {
        "psr-4": {
          "wpf\\": "vendor/nikonorovsv/wpf/",
          "app\\": "app/"
        }
      },
      "require": {
        "nikonorovsv/wpf": "dev"
      },
      "repositories":[
        {
          "type":"package",
          "package":{
            "name":"nikonorovsv/wpf",
            "version":"dev",
            "source":{
              "type":"git",
              "url":"http://github.com/nikonorovsv/wpf",
              "reference":"master"
            }
          }
        }
      ]
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

class QuerySetter
    extends Observer {

    public function doUpdate( App $app ) {

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
The code into `doUpdate()` method will be executed at runtime.

## Widgets
All parts in the WPF that you want to display on the page are recommended to be formatted as widgets. A widget is an object of a class inherited from the `\wpf\base\Widget` class. Widget classes should be located in the `/app/widgets` folder. Widgets can generate their own html, or use templates. You can override `$this->render()` method to return html without using of templete. Let's look at the code.
```php
namespace app\widgets;

use \wpf\base\Widget;

class SomeWidget extends Widget {

    public $template = 'widget';

    public function __construct( array $conf = [] ) {
        parent::__construct( $conf );

        $this->title = 'Some title';
        $this->content = 'Some text';
    }

    public function render():string {
        return parent::render();
    }
}
```

Templates should be located in `/app/views` folder and look like this:
```php

// Declare defaults. They will be replace with values defined by widget.
extract( [
    'title'   => '',
    'content' => ''
], EXTR_SKIP ); ?>

<div class="widget">
    <div class="widget-title">
        <?= $title ?>
    </div>
    <div class="widget-content">
        <?= $content ?>
    </div>
</div>
```
    
Widgets can use `\wpf\wp\QueryBuilder` trait for easy way to include some query to DB.
```php
namespace app\widgets;

use \wpf\base\Widget;
use \wpf\wp\QueryBuilder;

class SomeWidget extends Widget {

    use QueryBuilder;

    public $template = 'widget';

    public function __construct( array $conf = [] ) {
        parent::__construct( $conf );

        $this->items = $this->query()->posts;
    }

    public function queryArgs(): array {
        return [
            'post_type'      => 'custom_post_type',
            'post_status'    => 'publish',
            'posts_per_page' => 10
        ];
    }
}
```

Next, you simply call the widget in the right place. The passed parameters will be available as class properties after the parent constructor is called. Then you can do with them whatever you want.
```php
use \app\widgets\SomeWidget;

echo (string) new SomeWidget([
    'title'   => 'Some title',
    'content' => 'Some text'
]);
```

## About
Hey! Do you have any ideas how to do WPF better? 
[Send me a letter](mailto:nikonorovsv@gmail.com).
  
    
