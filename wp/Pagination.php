<?php

namespace wpf\wp;

use \wpf\base\Component;
use \InvalidArgumentException;

/**
 * Class Pagination
 * @package app\widgets
 */
final class Pagination extends Component
{
    /**
     * Max quantity of pages.
     *
     * @var float|int
     */
    private $_max = 1;

    /**
     * It's True when the subject is WP_Query object;
     *
     * @var bool
     */
    private $_is_wp = false;

    /**
     * Current page nubmer
     *
     * @var
     */
    private $_current;

    /**
     * Pagination constructor.
     * Create this class before get_title() calling for correct counter working.
     * The "subject" parameter mast be set. It can be WP_Query object, array, or int
     *
     * @param array $conf
     */
    public function __construct( array $conf = [] ) {
        $conf = wp_parse_args( $conf, [
            'count'          => 5,
            'per_page'       => 10,
            'query_key'      => 'pg',
            'label_to_start' => __('В начало'),
            'label_next'     => __('Следующая >'),
        ] );
        parent::__construct( $conf );
        if ( ! isset( $this->subject ) ) {
            throw new InvalidArgumentException('The "subject" parameter must be set.');
        }
        if ( is_array( $this->subject ) ) {
            $this->subject = count( $this->subject );
        }
        if ( is_a( $this->subject, '\WP_Query') ) {
            $this->_is_wp = true;
            // Takes values from WordPress
            $this->_max = $this->subject->max_num_pages;
            $this->per_page = $this->subject->post_count;
        } elseif ( is_int( $this->subject ) || is_string( $this->subject ) ) {
            $this->_max = ceil( $this->subject / $this->per_page );
        }
        $this->_current = $this->current();
        // Save to the WPF Store
        if ( function_exists('app') ) {
            app()->pagination_state = $this->state();
        }
    }

    /**
     * This function creates links and save them to $this->_items array.
     * Each of links is array like [$link, $isActive]
     */
    public function items() {
        if ( $this->_max <= 1 ) {
            return [];
        }
        $end = min( $this->_max, $this->_current + floor( $this->count / 2 ) );
        $start = max( 1, $end - $this->count + 1 );
        $items = [];
        if ( $start != 1 ) {
            $items[] = $this->item( $this->label_to_start, 1);
        }
        for ( $i = $start; $i <= $end; $i ++ ) {
            $items[] = $this->item( $i, $i, ($i == $this->_current) );
        }
        if ( $this->_current != $this->_max ) {
            $items[] = $this->item( $this->label_next, ($this->_current + 1) );
        }
        return $items;
    }

    /**
     * Returns prev and next urls as associated array.
     *
     * @return array
     */
    public function pager() {
        $prev = ( $this->_current > 1 ) ? $this->url( $this->_current - 1 ) : false;
        $next = ( $this->_current < $this->_max ) ? $this->url( $this->_current + 1 ) : false;

        return compact('prev', 'next');
    }

    /**
     * Returns the indexed array [$current_page, $count_of_pages]
     * @return array
     */
    public function state(): array {
        return [ $this->current(), $this->_max ];
    }

    /**
     * @param $anchor
     * @param $page_number
     * @param bool $is_active
     * @return array
     */
    private function item( $anchor, $page_number, bool $is_active = false ): array {
        $values = [ $anchor, $this->url( $page_number ), $is_active ];
        return array_combine( ['anchor', 'url', 'isActive'], $values );
    }

    /**
     * @param int $page_number
     * @return string
     */
    private function url( int $page_number ): string {
        $url = home_url( $_SERVER['REQUEST_URI'] );
        if ( $page_number == 1 && ! $this->_is_wp ) {
            return remove_query_arg( $this->query_key, $url );
        }
        return add_query_arg( $this->_is_wp ? 'paged' : $this->query_key, $page_number, $url );
    }

    /**
     * It's know about current page number.
     *
     * @return int
     */
    public function current(): int {
        return max( absint( $_GET[ $this->query_key ] ?? 1 ), get_query_var('paged') );
    }
}
