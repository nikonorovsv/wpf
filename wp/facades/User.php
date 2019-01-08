<?php
namespace wpf\wp\facades;

use \WP_User;
use \wpf\helpers\WP;

/**
 * Class User
 * @package app\wp\facades
 */
class User
	implements IFacade {
	/**
	 * @var WP_User
	 */
	private $user;
	private $key;
	
	/**
	 * User constructor.
	 *
	 * @param int|NULL $user
	 */
	public function __construct( int $user = NULL ) {
		$user = $user ?? get_current_user_id();
		$this->user = get_user_by('id', $user );
		$this->key  = "user_{$user}";
	}
	
	/**
	 * @param $name
	 *
	 * @return bool|false|mixed|null|string
	 */
	public function __get( string $name ) {
		if ( $_name = strstr( $name, '_object', TRUE ) ) {
			return get_field_object( $_name, $this->key );
		} elseif ( $value = get_field( $name, $this->key ) ) {
			return $value;
		}
		return $this->user->$name ?? NULL;
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
		return $this->$name ? get_field_object( $name, $this->key )['label'] : FALSE;
	}

	/**
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function url( array $args = [] ) {
		$url = get_author_posts_url( $this->ID );
		if ( $args ) {
			$url = add_query_arg( $args, $url );
		}

		return $url;
	}
	
	/**
	 * @param string $size
	 *
	 * @return false|string
	 */
	public function thumbUrl( string $size = 'thumbnail') {
		$size = WP::getImageSize( $size );
		
		return get_avatar_url( $this->ID, ['size' => $size['width'] ] );
	}
	
	/**
	 * @return mixed
	 */
	public function editUrl() {
		return get_edit_user_link( $this->ID );
	}
	
	/**
	 * @param string $name
	 * @param $value
	 *
	 * @return bool|int|WP_Error
	 */
	public function update( string $name, $value ) {
		if ( $this->user->$name ) {
			return wp_update_user( [
				'ID'  => $this->ID,
				$name => $value
			] );
		} elseif ( $this->$name ) {
			return update_field( $name, $value, $this->key );
		}
		
		return FALSE;
	}

    /**
     * @param array $data
     *
     * @return int|\WP_Error
     */
	public static function create( array $data ) {
        return wp_insert_user( $data );
    }
}