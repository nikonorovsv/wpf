# WPF
WPF is a WordPress Framework to make yout creating of theme easy.

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
        "name": "nikonorovsv/wpf",
        "description": "WPF theme",
        "authors": [
            {
                "name": "Sergey Nikonorov",
                "email": "nikonorovsv@gmail.com"
            }
        ],
        "autoload": {
            "psr-4": {
                "wpf\\": "vendor/nikonorovsv/wpf/",
                "app\\": "app/"
            }
        },
        "require": {
            "nikonorovsv/wpf": "dev"
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

## About
Hey! Do you have any ideas how to do WPF better? 
[Send me a letter](mailto:nikonorovsv@gmail.com).
  
    