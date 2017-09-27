<?php
namespace wpf\app\observers;

use \wpf\App;
use \wpf\app\Observer;
use \wpf\helpers\WP;

/**
 * Class AssetsDefiner
 * @package wpf\app\observers
 */
class AssetsDefiner
	extends Observer {
	/**
	 * @param App $app
	 */
	public function doUpdate( App $app ) {
		$update = function () use ( $app ) {
			// Deregister custom styles
			if ( $app->deregister_styles ) {
				foreach ( $app->deregister_styles as $style ) {
					wp_deregister_style( $style );
				}
			}
			// Register and Set styles
			if ( $app->register_styles ) {
				$enqueue_styles = [];
				foreach ( $app->register_styles as $style ) {
					if ( ! $style[ 'cdn' ] ) {
						$style[ 'src' ] = WP::uri( $style[ 'src' ] );
					}
					wp_register_style( $style[ 'name' ], $style[ 'src' ],
						$style[ 'deps' ] ?? [],
						$style[ 'ver' ] ?? FALSE,
						$style[ 'media' ] ?? 'all'
					);
					if ( $style[ 'enqueue' ] ) {
						$enqueue_styles[] = $style[ 'name' ];
					}
				}
				array_map( function ( $item ) {
					wp_enqueue_style( $item );
				}, $enqueue_styles );
			}
			// Deregister custom scripts
			if ( $app->deregister_scripts ) {
				foreach ( $app->deregister_scripts as $script ) {
					wp_deregister_script( $script );
				}
			}
			// Register and Set scripts
			if ( $app->register_scripts ) {
				$enqueue_scripts = [];
				foreach ( $app->register_scripts as $script ) {
					if ( ! $script[ 'cdn' ] ) {
						$script[ 'src' ] = WP::uri(  $script[ 'src' ] );
					}
					wp_register_script( $script[ 'name' ], $script[ 'src' ],
						$script[ 'deps' ] ?? [],
						$script[ 'ver' ] ?? FALSE,
						$script[ 'in_footer' ] ?? FALSE
					);
					if ( $script[ 'enqueue' ] ) {
						$enqueue_scripts[] = $script[ 'name' ];
					}
				}
				array_map( function ( $item ) {
					wp_enqueue_script( $item );
				}, $enqueue_scripts );
			}
		};
		add_action( 'wp_enqueue_scripts', $update );
	}
}