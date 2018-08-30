<?php

namespace wpf\wp\facades;

use \WP_Post;
use \WP_Error;
use \wpf\helpers\Html;
use \wpf\helpers\Date;
use \wpf\helpers\WP;

/**
 * Class Post
 * @package wpf\wp\facades
 */
class Post
	implements IFacade {
	/**
	 * @var array object
	 */
	private $post;
	
	/**
	 * Post constructor.
	 *
	 * @param int|WP_Post|null $post Optional. Post ID or post object. Defaults to global $post.
	 */
	public function __construct( $post = NULL ) {
		$this->post = get_post( $post );
	}
	
	/**
	 * @param $name
	 *
	 * @return mixed|bool
	 */
	public function __get( string $name ) {
		if ( $_name = strstr( $name, '_object', TRUE ) ) {
			return get_field_object( $_name, $this->post );
		} elseif ( $value = get_field( $name, $this->post ) ) {
			return $value;
		}
		
		return $this->post->$name ?? NULL;
	}
	
	/**
	 * @param $name
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
		return $this->$name ? get_field_object( $name, $this->ID )[ 'label' ] : FALSE;
	}

	/**
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function url( array $args = [] ) {
		$url = get_permalink( $this->ID );
		if ( $args ) {
			$url = add_query_arg( $args, $url );
		}

		return $url;

	}

	/**
	 * @return \DateTime|false
	 */
	public function pubDate() {
		return new Date( get_the_time( 'Y-m-d', $this->ID ) );
	}
	
	/**
	 * @return mixed
	 */
	public function title() {
		return $this->post_title;
	}
	
	/**
	 * @param int $length
	 * @param string $more
	 *
	 * @return array|string
	 */
	public function excerpt( int $length = 0, string $more = '...' ) {
		$excerpt = get_the_excerpt( $this->ID );
		if ( $length ) {
			$arr = explode(' ', $excerpt );
			$arr = array_slice( $arr, 0, $length );
			return join( ' ', $arr ) . $more;
		}
		
		return $excerpt;
	}
	
	/**
	 * @return mixed
	 */
	public function content() {
		return apply_filters( 'the_content', $this->post_content );
	}
	
	/**
	 * @return int
	 */
	public function countComments() {
		return get_comments_number( $this->ID );
	}
	
	/**
	 * @return mixed
	 */
	public function editUrl() {
		return get_edit_post_link( $this->ID );
	}
	
	/**
	 * @return mixed
	 */
	public function deleteUrl() {
		return get_delete_post_link( $this->ID );
	}
	
	/**
	 * @param string $name
	 * @param $value
	 *
	 * @return bool|int|WP_Error
	 */
	public function update( string $name, $value ) {
		if ( $this->post->$name ) {
			return wp_update_post( [
				'ID'  => $this->ID,
				$name => $value
			] );
		} elseif ( $this->$name ) {
			return update_field( $name, $value, $this->ID );
		}
		
		return FALSE;
	}
	
	/**
	 * @param $taxonomies
	 * @param array $args
	 *
	 * @return array|WP_Error
	 */
	public function terms( $taxonomies, array $args = [] ) {
		return wp_get_object_terms( $this->ID, $taxonomies, wp_parse_args( $args, [
			'fields' => 'all'
		] ) );
	}
	
	/**
	 * @param string $taxonomy
	 *
	 * @return array
	 */
	public function termLinks( string $taxonomy ): array {
		$res   = [];
		$terms = $this->terms( $taxonomy );
		if ( is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				$res[] = Html::a( $term->name, get_term_link( $term->term_id ) );
			}
		}
		
		return $res;
	}
	
	/**
	 * @param string $size
	 * @param bool $placeholder
	 *
	 * @return bool|false|string
	 */
	public function thumbUrl( string $size = 'thumbnail', bool $placeholder = TRUE ) {
		if ( post_password_required( $this->ID ) ) {
			return FALSE;
		}
		$src = get_the_post_thumbnail_url( $this->ID, $size );
		if ( ! $src && $placeholder ) {
			$size = WP::getImageSize( $size );
			$src  = "http://placehold.it/{$size['width']}x{$size['height']}?text=Нет+изображения";
		}
		
		return $src;
	}

       /**
         * @return bool
         */
	public function hasThumb() {
	        return has_post_thumbnail( $this->ID );
        }
	
	/**
	 * @return false|string
	 */
	public function postType() {
		return get_post_type( $this->ID );
	}
	
	/**
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function postTypeLabel( string $name = 'singular_name' ) {
		$obj = get_post_type_object( $this->postType() );
		
		return $obj->labels->$name ?? '';
	}
	
	/**
	 * @return array
	 */
	public function parents() {
		return get_post_ancestors( $this->ID );
	}
	
	/**
	 * @param array $args
	 *
	 * @return array
	 */
	public function children( array $args = [] ) {
		$args = wp_parse_args( $args, [
			'post_parent' => $this->ID,
		    'fields'      => 'ids',
		    'orderby'     => 'menu_order',
		    'order'       => 'ASC'
		] );
		
		return get_children( $args );
	}
}
