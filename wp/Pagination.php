<?php

namespace wpf\wp;

use \wpf\helpers\Html;

/**
 * Class Pagination
 * @package app\widgets
 */
class Pagination
{
    public $per_page  = 10;
    public $query_key = 'gp';
    public $quantity  = 5;

    private $_items = [];

    /**
     * Pagination constructor.
     *
     * @param $subject \WP_Query int array
     */
    public function __construct( $subject ) {
        $count = 1;
        $items = [];
        if ( $subject ) {
            if ( is_array( $subject ) ) {
                $subject = count( $subject );
            }
            if ( is_a( $subject, '\WP_Query' ) ) {
                $count = $subject->max_num_pages;
                $items = $this->forQuery( $count );
            } elseif ( is_int( $subject ) ) {
                $count = ceil( $subject / $this->per_page );
                $items = $this->forCount( $count );
            }
        }
        app()->max_page = $count;
        $this->_items   = $items;
    }

    /**
     * @return array
     */
    public function items() {
        return $this->_items;
    }

    /**
     * Call this function before get_title() for correct counter working,
     *
     * @param int $count
     *
     * @return array
     */
    public function forQuery( int $count ) {
        if ( $count <= 1 ) {
            return [];
        }
        $pages = paginate_links( [
            'base'      => str_replace( 999999999, '%#%', get_pagenum_link( 999999999 ) ),
            'format'    => '?page=%#%',
            'current'   => max( 1, get_query_var( 'paged' ) ),
            'total'     => $count,
            'end_size'  => 3,
            'type'      => 'array',
            'prev_next' => TRUE,
            'prev_text' => '< Предыдущая',
            'next_text' => 'Следующая >'
        ] );
        $items = [];
        foreach ( (array) $pages as $item ) {
            $items[] = [ $item, strpos( $item, 'current' ) ];
        }

        return $items;
    }

    /**
     * Call this function before get_title() for correct counter working,
     *
     * @param int $count
     *
     * @return array
     */
    public function forCount( int $count ) {
        if ( $count <= 1 ) {
            return [];
        }
        $request = home_url( $_SERVER[ 'REQUEST_URI' ] );
        $current = $_GET[ $this->query_key ] ? absint( $_GET[ $this->query_key ] ) : 1;
        $end     = min( $count, $current + floor( $this->quantity / 2 ) );
        $start   = max( 1, $end - $this->quantity + 1 );
        $item_options = [];
        $items   = [];
        if ( $start != 1 ) {
            $items[] = (array) Html::a(
                'В начало',
                add_query_arg( $this->query_key, 1, $request )
            );
        }
        for ( $i = $start; $i <= $end; $i ++ ) {
            $items[] = [ Html::a(
                $i,
                add_query_arg( $this->query_key, $i, $request )
            ), $i == $current ];
        }
        if ( $current != $count ) {
            $items[] = (array) Html::a(
                'Следующая >',
                add_query_arg( $this->query_key, $current + 1, $request )
            );
        }

        return $items;
    }

    /**
     * @return int
     */
    public static function max(): int {
        return app()->max_page;
    }

    /**
     * @param string $key
     * @return int
     */
    public static function current( string $key = 'gp' ): int {
        return max( $_GET[ $key ] ? absint( $_GET[ $key ] ) : 1, get_query_var( 'paged' ) );
    }
}
