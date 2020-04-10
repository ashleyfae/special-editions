<?php
/**
 * Database
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Editions;

/**
 * Class Database
 *
 * @package SpecialEditions\Editions
 */
class Database extends \SpecialEditions\Database {

    /**
     * Retrieves the query interface
     *
     * @param array $args
     *
     * @return Database\Query
     */
    public static function getQuery( $args = array() ) {
        return new Database\Query( $args );
    }

    /**
     * Retrieves a single special edition by its ID
     *
     * @param int $id ID of the edition to retrieve.
     *
     * @since 1.0
     * @return Edition
     * @throws \Exception
     */
    public static function retrieve( $id ) {
        return parent::retrieve( $id );
    }

    /**
     * Retrieves a single special edition by a column name/value combo
     *
     * @param string $column_name Name of the column to search in.
     * @param mixed  $value       Value of the column.
     *
     * @since 1.0
     * @return Edition
     * @throws \Exception
     */
    public static function retrieveBy( $column_name, $value ) {
        return parent::retrieveBy( $column_name, $value );
    }

    /**
     * Queries for special editions
     *
     * @param array $args    {
     *                       Query arguments. All optional.
     *
     * @type int    $number  Maximum number of results.
     * @type string $orderby Column name to order results by.
     * @type string $order   How to order results. Default DESC.
     * }
     *
     * @since 1.0
     * @return Edition[]
     */
    public static function query( $args = array() ) {
        return parent::query( $args );
    }

    /**
     * Creates a new special edition
     *
     * @param array $args
     *
     * @since 1.0
     * @return Edition
     * @throws \Exception
     */
    public static function create( $args ) {

        $args = wp_parse_args( $args, array(
            'book_id'       => null,
            'image_id'      => null,
            'price'         => null,
            'currency_code' => null,
            'available'     => 0,
            'url'           => '',
            'description'   => ''
        ) );

        if ( empty( $args['book_id'] ) ) {
            throw new \InvalidArgumentException( __( 'book_id is required.', 'special-editions' ) );
        }

        if ( ! empty( $args['price'] ) && empty( $args['currency_code'] ) ) {
            throw new \InvalidArgumentException( __( 'You must supply a currency code.', 'special-editions' ) );
        }

        if ( ! empty( $args['currency_code'] ) && null === $args['price'] ) {
            throw new \InvalidArgumentException( __( 'You must supply a price.', 'special-editions' ) );
        }

        return parent::create( $args );

    }

}