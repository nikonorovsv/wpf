<?php

namespace wpf\wp\facades;

use \WP_Comment_Query;
use \wpf\helpers\Date;

/**
 * Class Comment
 * @package wpf\wp\facades
 */
class Comment
    implements IFacade {
    /**
     * @var array object
     */
    private $comment;

    /**
     * Post constructor.
     *
     * @param int|WP_Comment|null $comment Optional.
     * Comment ID or comment object.
     * Defaults to global $comment.
     */
    public function __construct( $comment = NULL ) {
        $this->comment = get_comment( $comment );
    }

    /**
     * @param $name
     *
     * @return mixed|bool
     */
    public function __get( string $name ) {
        if ( $_name = strstr( $name, '_object', TRUE ) ) {
            return get_field_object( $_name, $this->comment );
        } elseif ( $value = get_field( $name, $this->comment ) ) {
            return $value;
        } elseif ( $this->comment->{"comment_{$name}"} ) {
            return $this->comment->{"comment_{$name}"};
        }

        return $this->comment->$name || NULL;
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
        return $this->$name ? get_field_object( $name, $this->comment )['label'] : FALSE;
    }

    /**
     * @param array $args
     *
     * @return mixed
     */
    public function url( array $args = []) {
        $url = get_comments_link( $this->post_ID );
        if ( $args ) {
            $url = add_query_arg( $args, $url );
        }

        return $url;
    }

    /**
     * @return Date
     */
    public function pubDate() {
        return new Date( $this->date );
    }

    /**
     * @return array
     */
    public function author() {
        return array_combine(
            ['name', 'email', 'site', 'IP'],
            [
                $this->author,
                $this->author_email,
                $this->author_url,
                $this->author_IP
            ]
        );
    }

    /**
     * @return mixed
     */
    public function content() {
        return $this->content;
    }

    /**
     * @return mixed
     */
    public function editUrl() {
        return get_edit_comment_link( $this->ID );
    }

    /**
     * @param string $name
     * @param $value
     *
     * @return bool|int|WP_Error
     */
    public function update( string $name, $value ) {
        if ( $this->comment->$name ) {
            return wp_update_comment([
                'comment_ID' => $this->ID,
                $name        => $value
            ]);
        } elseif ( $this->$name ) {
            return update_field( $name, $value, $this->comment );
        }

        return FALSE;
    }

    /**
     * @param int $size
     *
     * @return bool|false|string
     */
    public function avatarUrl( int $size = 96 ) {
        return get_avatar_url( $this->author_email, ['size' => $size ]);
    }

    /**
     * @return mixed
     */
    public function status() {
        return $this->approved;
    }

    /**
     * @return array
     */
    public function parent() {
        return $this->parent;
    }

    /**
     * @param array $args
     *
     * @return array
     */
    public function children( array $args = []) {
        $args = wp_parse_args( $args, [
            'parent'  => $this->ID,
            'fields'  => 'ids',
            'orderby' => 'comment_date_gmt',
            'order'   => 'ASC'
        ] );

        $query = new WP_Comment_Query;
        return $query->query( $args );
    }
}
