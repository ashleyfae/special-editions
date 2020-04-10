<?php
/**
 * Database.php
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions;

use Book_Database\BerlinDB\Database\Query;

/**
 * Class Database
 *
 * @package SpecialEditions
 */
abstract class Database {

    /**
     * Retrieves the query interface
     *
     * @param array $args
     *
     * @return Query
     */
    abstract static function getQuery( $args = array() );

    /**
     * Retrieves a single item by its ID
     *
     * @param int $id Primary key value
     *
     * @return object|mixed
     * @throws \Exception
     */
    public static function retrieve( $id ) {
        $item = static::getQuery()->get_item( $id );

        if ( is_object( $item ) ) {
            return $item;
        }

        throw new \Exception( __( 'Item not found.', 'special-editions' ) );
    }

    /**
     * Retrieves a single item by a column name/value combo
     *
     * @param string $column_name Name of the column to search in.
     * @param mixed  $value       Value of the column.
     *
     * @return object|mixed
     * @throws \Exception
     */
    public static function retrieveBy( $column_name, $value ) {
        $item = static::getQuery()->get_item_by( $column_name, $value );

        if ( is_object( $item ) ) {
            return $item;
        }

        throw new \Exception( __( 'Item not found.', 'special-editions' ) );
    }

    /**
     * Queries for items
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
     * @return array
     */
    public static function query( $args = array() ) {
        $args = wp_parse_args( $args, array(
            'number' => 20
        ) );

        return static::getQuery()->query( $args );
    }

    /**
     * Counts the number of items
     *
     * @param array $args
     *
     * @since 1.0
     * @return int
     */
    public static function count( $args = array() ) {
        $args = wp_parse_args( $args, array(
            'count' => true
        ) );

        $count = static::getQuery( $args )->found_items;

        return absint( $count );
    }

    /**
     * Creates a new item
     *
     * @param array $args
     *
     * @return object|mixed
     * @throws \Exception
     */
    public static function create( $args ) {
        $item_id = static::getQuery()->add_item( $args );

        if ( empty( $item_id ) ) {
            throw new \Exception( __( 'Failed to insert item.', 'special-editions' ) );
        }

        return self::retrieve( $item_id );
    }

    /**
     * Updates an item
     *
     * @param int   $item_id ID of the item to update.
     * @param array $args    Data to update.
     *
     * @since 1.0
     * @return void
     * @throws \Exception
     */
    public static function update( $item_id, $args ) {
        $updated = static::getQuery()->update_item( $item_id, $args );

        if ( ! $updated ) {
            throw new \Exception( __( 'Failed to update item.', 'special-editions' ) );
        }
    }

    /**
     * Deletes an item
     *
     * @param int $item_id ID of the item to delete.
     *
     * @since 1.0
     * @return void
     * @throws \Exception
     */
    public static function delete( $item_id ) {

        $deleted = static::getQuery()->delete_item( $item_id );

        if ( ! $deleted ) {
            throw new \Exception( __( 'Failed to delete item.', 'special-editions' ) );
        }

    }

}