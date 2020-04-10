<?php
/**
 * Query.php
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Editions;

/**
 * Class Query
 *
 * @package SpecialEditions\Editions
 */
class Query {

    /**
     * @var array Query arguments
     */
    protected $args = array();

    protected $total_count = 0;

    protected $editions = array();

    /**
     * Queries for special editions
     *
     * @param array $args
     */
    public function __construct( $args = array() ) {

        global $wpdb;

        $this->args = wp_parse_args( $args, array(
            'title'     => '',
            'author'    => '',
            'age'       => '',
            'genre'     => '',
            'attribute' => '',
            'available' => '',
            'per_page'  => 20,
            'page'      => 1
        ) );

        $joins = array(
            "LEFT JOIN {$wpdb->bdb_book_author_relationships} AS ar ON( book.id = ar.book_id )",
            "LEFT JOIN {$wpdb->bdb_authors} AS author ON( ar.author_id = author.id )",
            "INNER JOIN {$wpdb->bdb_special_editions} AS edition ON( book.id = edition.book_id )"
        );

        // If we have an age or genre condition, join on terms table.
        if ( $this->args['age'] || $this->args['genre'] ) {
            $joins[] = "LEFT JOIN {$wpdb->bdb_book_term_relationships} AS btr ON( book.id = btr.book_id )";
        }

        // If we have an attribute condition, join on the attribute table.
        if ( $this->args['attribute'] ) {
            $joins[] = "LEFT JOIN {$wpdb->bdb_special_edition_attribute_relationships} AS attrr ON( edition.id = attrr.edition_id )";
        }

        $where = array();

        // Title search
        if ( ! empty( $this->args['title'] ) ) {
            $where[] = $wpdb->prepare( "book.title LIKE %s", '%' . $wpdb->esc_like( $this->args['title'] ) . '%' );
        }

        // Author search
        if ( ! empty( $this->args['author'] ) ) {
            $where[] = $wpdb->prepare( "author.name LIKE %s", '%' . $wpdb->esc_like( $this->args['author'] ) . '%' );
        }

        // Age group search
        if ( ! empty( $this->args['age'] ) ) {
            $where[] = $wpdb->prepare( "btr.term_id = %d", absint( $this->args['age'] ) );
        }

        // Genre search
        if ( ! empty( $this->args['genre'] ) ) {
            $where[] = $wpdb->prepare( "btr.term_id = %d", absint( $this->args['genre'] ) );
        }

        // Attribute search
        if ( ! empty( $this->args['attribute'] ) ) {
            $where[] = $wpdb->prepare( "attrr.attribute_id = %d", absint( $this->args['attribute'] ) );
        }

        // Available books only.
        if ( ! empty( $this->args['available'] ) ) {
            $where[] = "edition.available = 1";
        }

        $join_string  = implode( ' ', $joins );
        $where_string = implode( ' AND ', $where );

        if ( ! empty( $where_string ) ) {
            $where_string = ' WHERE ' . $where_string;
        }

        $query = $wpdb->prepare( "
            SELECT SQL_CALC_FOUND_ROWS
            book.title AS title,
            GROUP_CONCAT( DISTINCT author.name SEPARATOR ',' ) as author_name,
            edition.*
            FROM {$wpdb->bdb_books} AS book
            {$join_string}
            {$where_string}
            GROUP BY edition.id
            ORDER BY edition.date_created DESC
            LIMIT %d, %d",
            ( absint( $this->args['page'] ) - 1 ) * $this->args['per_page'],
            $this->args['per_page']
        );

        //error_log( $query );

        $this->editions    = $wpdb->get_results( $query );
        $this->total_count = $wpdb->get_var( "SELECT FOUND_ROWS()" );

    }

    /**
     * Determines whether or not editions have been located
     *
     * @return bool
     */
    public function hasEditions() {
        return ! empty( $this->editions );
    }

    /**
     * Returns the query results
     *
     * @return object[]
     */
    public function getEditions() {
        return $this->editions;
    }

    /**
     * Returns the total number of results (`SQL_CALC_COUNT_ROWS`)
     *
     * @return int
     */
    public function getTotalCount() {
        return $this->total_count;
    }

    /**
     * Returns the number of results per page
     *
     * @return int
     */
    public function getPerPage() {
        return $this->args['per_page'];
    }

}