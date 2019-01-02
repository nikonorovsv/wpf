<?php
namespace wpf\app\observers;

use \wpf\App;
use \wpf\app\Observer;

/**
 * Class AdminAssetsDefiner
 * @package wpf\app\observers
 */
class AdminAssetsDefiner
	extends Observer {
	/**
	 * @param App $app
	 */
	public function doUpdate( App $app ) {
		$update = function () use ( $app ) {
			// Register and Set admin styles
			if ( $app->register_admin_styles ) {
				$enqueue_styles = [];
				foreach ( $app->register_admin_styles as $style ) {
					wp_register_style( $style['name'], $style[ 'src' ],
						$style['deps'] ?? [],
						$style['ver'] ?? FALSE,
						$style['media'] ?? 'all'
					);
					if ( ! empty( $style['enqueue'] ) ) {
						$enqueue_styles[] = $style['name'];
					}
				}
				array_map( function ( $item ) {
					wp_enqueue_style( $item );
				}, $enqueue_styles );
			}
		};
		add_action('admin_head', $update );
	}
}

