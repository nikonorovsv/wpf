<?php

namespace wpf\app\observers;

use wpf\app\Observer;
use wpf\App;

/**
 * Class DisableRESTAPI
 * @package \app\observers
 */
class DisableRESTAPI
  extends Observer
{

  /**
   * @param App $app
   */
  public function doUpdate(App $app)
  {
    // Disable REST API
    add_filter( 'rest_enabled', '__return_false' );

    // Disable REST API filters
    remove_action( 'xmlrpc_rsd_apis',            'rest_output_rsd' );
    remove_action( 'wp_head',                    'rest_output_link_wp_head', 10 );
    remove_action( 'template_redirect',          'rest_output_link_header', 11 );
    remove_action( 'auth_cookie_malformed',      'rest_cookie_collect_status' );
    remove_action( 'auth_cookie_expired',        'rest_cookie_collect_status' );
    remove_action( 'auth_cookie_bad_username',   'rest_cookie_collect_status' );
    remove_action( 'auth_cookie_bad_hash',       'rest_cookie_collect_status' );
    remove_action( 'auth_cookie_valid',          'rest_cookie_collect_status' );
    remove_filter( 'rest_authentication_errors', 'rest_cookie_check_errors', 100 );

    // Disable REST API events
    remove_action( 'init',          'rest_api_init' );
    remove_action( 'rest_api_init', 'rest_api_default_filters', 10 );
    remove_action( 'parse_request', 'rest_api_loaded' );

    // Disable REST API Embeds
    remove_action( 'rest_api_init',          'wp_oembed_register_route'              );
    remove_filter( 'rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10 );

    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    // Comment it if you want to use embeds from another sites
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );
  }
}
