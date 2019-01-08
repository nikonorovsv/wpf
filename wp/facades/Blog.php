<?php
namespace app\wp\facades;

use \WP_Site;
use \WP_Error;

/**
 * Class Blog
 * @package app\wp\facades
 */
class Blog
	implements IFacade {
	/**
	 * @var WP_Site
	 */
	private $blog;
	
	/**
	 * Blog constructor.
	 *
	 * @param int|NULL $blog
	 */
	public function __construct( int $blog = NULL ) {
		$blog = $blog ?? get_current_blog_id();
		$this->blog = WP_Site::get_instance( $blog );
	}
	
	/**
	 * @param $name
	 *
	 * @return bool|false|mixed|null|string
	 */
	public function __get( string $name ) {
		$value = NULL;
		if ( $this->blog->$name ) :
			return $this->blog->$name;
		elseif ( $value = get_bloginfo( $name ) ) :
		elseif ( $value = get_option( $name ) ) :
		elseif ( $value = get_field( $name, 'option') ) :
		endif;
		
		return $value;
	}
	
	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function __isset( string $name ) {
		return isset( $this->$name );
	}
	
	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function label( string $name ) {
		return $this->blogname;
	}

	/**
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function url( array $args = [] ) {
		$url = $this->siteurl;
		if ( $args ) {
			$url = add_query_arg( $args, $url );
		}

		return $url;
	}
	
	/**
	 * @param string $path
	 * @param string $scheme
	 *
	 * @return mixed
	 */
	public function editUrl( $path = '', $scheme = 'admin') {
		return get_admin_url( $this->id, $path, $scheme );
	}
	
	/**
	 * @param string $name
	 * @param $value
	 *
	 * @return bool|int|WP_Error
	 */
	public function update( string $name, $value ) {
		if ( get_field( $name, 'option') ) {
			return update_field( $name, $value, 'option');
		} elseif ( get_option( $name ) ) {
			return update_option( $name, $value );
		}
		
		return FALSE;
	}

    /**
     * @param array $data
     * @return int|WP_Error
     */
	public static function create( array $data ) {
        if ( empty( $data['domain'] ) ) {
            return new WP_Error('missing_domain',
                __("The 'domain' parameter can't sent.", PREFIX ) );
        }
        if ( empty( $data['path'] ) ) {
            return new WP_Error('missing_path',
                __("The 'path' parameter can't sent.", PREFIX ) );
        }
        if ( empty( $data['title'] ) ) {
            return new WP_Error('missing_title',
                __("The 'title' parameter can't sent.", PREFIX ) );
        }
        if ( empty( $data['user_id'] ) ) {
            return new WP_Error('missing_user_id',
                __("The 'user_id' parameter can't sent.", PREFIX ) );
        }

	    return wpmu_create_blog(
	        $data['domain'],
            $data['path'],
            $data['title'],
            $data['user_id'],
            $data['meta'] ?? [],
            $data['network_id'] ?? 1
        );
    }
}