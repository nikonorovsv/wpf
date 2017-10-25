<?php

namespace wpf\wp;

use \wpf\helpers\ArrayHelper;
use \wpf\helpers\Html;

/**
 * Class Crumbs
 * @package wpf\wp
 */
class Crumbs {

	const PREFIX = 'wpf-crumbs';

	private $_post;
	private $_items = [];
	private $_faq = [
		//'home'            => 'home',
		'404'               => 'notFound',
		'search'            => 'search',
		'tax'               => 'tax',
		'attachment'        => 'attachment',
		'singular'          => 'singular',
		'category'          => 'category',
		'tag'               => 'tag',
		'author'            => 'author',
		'day'               => 'day',
		'month'             => 'month',
		'year'              => 'year',
		'post_type_archive' => 'postTypeArchive',
	];

	/**
	 * Crumbs constructor.
	 */
	public function __construct() {
		global $post;
		$this->_post = $post;

		// Generate crumbs array
		$this->home();
		$this->applyRules();
		foreach ( $this->_faq as $condition => $method ) {
			$condition = "is_{$condition}";
			if ( function_exists( $condition ) && call_user_func( $condition ) ) {
				$this->{$method}();
			}
		}
	}

	/**
	 * @return array
	 */
	public function items(): array {
		return $this->_items;
	}

	/**
	 * @return void
	 */
	private function applyRules() {
		if ( is_front_page() || is_page() ) {
			return;
		} elseif ( is_home() && ! get_query_var( 'pagename' ) ) {
			return;
		} elseif ( is_category() ) {
			$tax = get_taxonomy( 'category' );
			if ( count( $tax->object_type ) != 1 || $tax->object_type[0] != 'post' ) {
				return;
			}
		} elseif ( is_tag() ) {
			$tax = get_taxonomy( 'post_tag' );
			if ( count( $tax->object_type ) != 1 || $tax->object_type[0] != 'post' ) {
				return;
			}
		} elseif ( is_tax() ) {
			$tax = get_taxonomy( get_query_var( 'taxonomy' ) );
			if ( count( $tax->object_type ) != 1 || $tax->object_type[0] != 'post' ) {
				return;
			}
		} else {
			$post_type = get_query_var( 'post_type' ) ?: 'post';
			if ( $post_type != 'post' ) {
				return;
			}
		}
		if ( get_option( 'show_on_front' ) == 'page' && $post_id = get_option( 'page_for_posts' ) ) {
			$post = get_post( $post_id );
			$this->addCrumb( $post->post_title, get_permalink( $post ) );
		}
	}

	/**
	 * Add Home Crumb
	 */
	private function home() {
		$this->addCrumb( $this->label( 'home' ), home_url( '/' ) );
	}

	/**
	 * Add 404 Crumb
	 */
	private function notFound() {
		$this->addCrumb( $this->label( '404' ) );
	}

	/**
	 * Add Search Result Crumb
	 */
	private function search() {
		$this->addCrumb( $this->label( 'search', get_search_query() ) );
	}

	/**
	 * Add Tax Crumb
	 */
	private function tax() {
		$tax  = get_query_var( 'taxonomy' );
		$term = get_term_by( 'slug', get_query_var( 'term' ), $tax );
		if ( is_taxonomy_hierarchical( $tax ) && $term->parent != 0 ) {
			$ancestors = array_reverse( get_ancestors( $term->term_id, $tax ) );
			foreach ( $ancestors as $ancestor_id ) {
				$ancestor = get_term( $ancestor_id, $tax );
				$this->addCrumb( $ancestor->name, get_term_link( $ancestor, $tax ) );
			}
		}
		$this->addCrumb(
			$this->label( 'taxonomy', $term->name ),
			get_term_link( $term->term_id, $tax )
		);
	}

	/**
	 * Add Attachment Crumbs
	 */
	private function attachment() {
		if ( $this->_post->post_parent ) {
			if ( $parent_post = get_post( $this->_post->post_parent ) ) {
				$this->addHierarchyOfSingularCrumbs( $parent_post );
				$this->addCrumb(
					get_the_title( $parent_post ),
					get_permalink( $parent_post )
				);
			}
		}
		$this->addCrumb(
			$this->label( 'attachment', get_the_title( $this->_post ) ),
			get_permalink( $this->_post )
		);
	}

	/**
	 * Add Singular Crumbs
	 */
	private function singular() {
		if ( is_front_page() ) {
			return;
		}
		$this->addHierarchyOfSingularCrumbs( $this->_post );
		$this->addCrumb(
			get_the_title( $this->_post ),
			get_permalink( $this->_post )
		);
	}

	/**
	 * Add Category Crumb
	 */
	private function category() {
		global $cat;

		$category = get_category( $cat );
		if ( $category->parent != 0 ) {
			$ancestors = array_reverse( get_ancestors( $category->term_id, 'category' ) );
			foreach ( $ancestors as $ancestor_id ) {
				$ancestor = get_category( $ancestor_id );
				$this->addCrumb( $ancestor->name, get_category_link( $ancestor->term_id ) );
			}
		}
		$this->addCrumb( $this->label( 'category', $category->name ), get_category_link( $cat ) );
	}

	/**
	 * Add Tag Crumb
	 */
	private function tag() {
		global $tag_id;
		$tag = get_tag( $tag_id );

		$this->addCrumb(
			$this->label( 'tag', $tag->name ),
			get_tag_link( $tag_id )
		);
	}

	/**
	 * Add Author Crumb
	 */
	private function author() {
		$author      = get_query_var( 'author' );
		$author_name = get_the_author_meta( 'display_name', $author );

		$this->addCrumb(
			$this->label( $author_name ),
			get_author_posts_url( $author )
		);
	}

	/**
	 * Add Day Crumb
	 */
	private function day() {
		if ( $m = get_query_var( 'm' ) ) {
			$year  = substr( $m, 0, 4 );
			$month = substr( $m, 4, 2 );
			$day   = substr( $m, 6, 2 );
		} else {
			$year  = get_query_var( 'year' );
			$month = get_query_var( 'monthnum' );
			$day   = get_query_var( 'day' );
		}
		$month_title = $this->getMonthTitle( $month );

		$this->addCrumb( $this->label( 'year', $year ), get_year_link( $year ) );
		$this->addCrumb( $this->label( 'month', $month_title ), get_month_link( $year, $month ) );
		$this->addCrumb( $this->label( 'day', $day ), get_day_link( $year, $month, $day ) );
	}

	/**
	 * Add Month Crumb
	 */
	private function month() {
		if ( $m = get_query_var( 'm' ) ) {
			$year  = substr( $m, 0, 4 );
			$month = substr( $m, 4, 2 );
		} else {
			$year  = get_query_var( 'year' );
			$month = get_query_var( 'monthnum' );
		}
		$month_title = $this->getMonthTitle( $month );

		$this->addCrumb( $this->label( 'year', $year ), get_year_link( $year ) );
		$this->addCrumb( $this->label( 'month', $month_title ), get_month_link( $year, $month ) );
	}

	/**
	 * ADd Year Crumb
	 */
	private function year() {
		if ( $m = get_query_var( 'm' ) ) {
			$year = substr( $m, 0, 4 );
		} else {
			$year = get_query_var( 'year' );
		}
		$this->addCrumb( $this->label( 'year', $year ), get_year_link( $year ) );
	}

	/**
	 * Add Post Type Archive Crumb
	 */
	private function postTypeArchive() {
		$post_type = get_post_type_object( get_query_var( 'post_type' ) );

		$this->addCrumb(
			$this->label( 'post_type', $post_type->label ),
			get_post_type_archive_link( $post_type->name )
		);
	}

	/**
	 * @param string $label
	 * @param string|null $url
	 */
	public function addCrumb( string $label, string $url = null ) {
		$this->_items[] = compact( 'label', 'url' );
	}

	/**
	 * Add Singular Hierarchy Crumbs
	 *
	 * @param $post
	 *
	 * @return array
	 */
	private function addHierarchyOfSingularCrumbs( $post ) {
		$post_type = get_post_type_object( $post->post_type );
		if ( $post_type && $post_type->has_archive ) {
			$this->addCrumb(
				$this->label( 'post_type', $post_type->label ),
				get_post_type_archive_link( $post_type->name )
			);
		}
		if ( is_post_type_hierarchical( $post_type->name ) ) {
			$ancestors = array_reverse( get_post_ancestors( $post ) );
			if ( $ancestors ) {
				$ancestor_posts = get_posts( [
					'post_type' => $post_type->name,
					'include'   => join( ',', $ancestors )
				] );
				foreach ( $ancestors as $ancestor ) {
					foreach ( $ancestor_posts as $ancestor_post ) {
						if ( $ancestor == $ancestor_post->ID ) {
							$this->addCrumb( $ancestor_post->post_title, get_permalink( $ancestor_post ) );
						}
					}
				}
			}
		} else {
			$post_type_taxonomies = get_object_taxonomies( $post_type->name, false );
			if ( is_array( $post_type_taxonomies ) && count( $post_type_taxonomies ) ) {
				foreach ( $post_type_taxonomies as $tax_slug => $taxonomy ) {
					if ( $taxonomy->hierarchical ) {
						$terms = get_the_terms( $post->ID, $tax_slug );
						if ( $terms ) {
							$term = array_shift( $terms );
							if ( $term->parent != 0 ) {
								$ancestors = array_reverse( get_ancestors( $term->term_id, $tax_slug ) );
								foreach ( $ancestors as $ancestor_id ) {
									$ancestor = get_term( $ancestor_id, $tax_slug );
									$this->addCrumb( $ancestor->name, get_term_link( $ancestor, $tax_slug ) );
								}
							}
							$this->addCrumb( $term->name, get_term_link( $term, $tax_slug ) );
							break;
						}
					}
				}
			}
		}
	}

	/**
	 * @param int $monthnum
	 *
	 * @return int
	 */
	private function getMonthTitle( int $monthnum = 0 ) {
		global $wp_locale;
		$date_format = get_option( 'date_format' );
		if ( in_array( $date_format, [
			'DATE_COOKIE',
			'DATE_RFC822',
			'DATE_RFC850',
			'DATE_RFC1036',
			'DATE_RFC1123',
			'DATE_RFC2822',
			'DATE_RSS'
		] ) ) {
			$month_format = 'M';
		} elseif ( in_array( $date_format, [
			'DATE_ATOM',
			'DATE_ISO8601',
			'DATE_RFC3339',
			'DATE_W3C'
		] ) ) {
			$month_format = 'm';
		} else {
			preg_match( '/(^|[^\\\\]+)(F|m|M|n)/', str_replace( '\\\\', '', $date_format ), $m );
			if ( isset( $m[2] ) ) {
				$month_format = $m[2];
			} else {
				$month_format = 'F';
			}
		}

		switch ( $month_format ) {
			case 'F' :
				$month = $wp_locale->get_month( $monthnum );
				break;
			case 'M' :
				$month = $wp_locale->get_month_abbrev( $wp_locale->get_month( $monthnum ) );
				break;
			default :
				$month = $monthnum;
				break;
		}

		return $month;
	}

	/**
	 * @param string $name
	 * @param string|null $value
	 *
	 * @return bool|mixed
	 */
	private function label( string $name, string $value = null ) {
		$labels = [
			'home'       => __( 'Главная', self::PREFIX ),
			'search'     => __( 'Результаты поиска по запросу: "%s"', self::PREFIX ),
			'404'        => __( '404. Страница не найдена', self::PREFIX ),
			'category'   => _x( '%s', 'category label', self::PREFIX ),
			'tag'        => _x( '%s', 'tag label', self::PREFIX ),
			'taxonomy'   => _x( '%s', 'taxonomy label', self::PREFIX ),
			'author'     => _x( '%s', 'author label', self::PREFIX ),
			'attachment' => _x( '%s', 'attachment label', self::PREFIX ),
			'year'       => _x( '%s', 'year label', self::PREFIX ),
			'month'      => _x( '%s', 'month label', self::PREFIX ),
			'day'        => _x( '%s', 'day label', self::PREFIX ),
			'post_type'  => _x( '%s', 'post type label', self::PREFIX )
		];

		if ( array_key_exists( $name, $labels ) ) {
			if ( ! is_null( $value ) ) {
				return sprintf( $labels[ $name ], $value );
			}

			return $labels[ $name ];
		}

		return false;
	}
}