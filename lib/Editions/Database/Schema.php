<?php
/**
 * Schema
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Editions\Database;

/**
 * Class Schema
 *
 * @package SpecialEditions\Editions\Database
 */
class Schema extends \Book_Database\BerlinDB\Database\Schema {

    /**
     * @var array
     */
    protected $columns = array(

        // id
        array(
            'name'     => 'id',
            'type'     => 'bigint',
            'length'   => '20',
            'unsigned' => true,
            'extra'    => 'auto_increment',
            'primary'  => true,
            'sortable' => true
        ),

        // book_id
        array(
            'name'     => 'book_id',
            'type'     => 'bigint',
            'length'   => '20',
            'unsigned' => true,
            'sortable' => true,
            'validate' => 'absint'
        ),

        // image_id
        array(
            'name'       => 'image_id',
            'type'       => 'bigint',
            'length'     => '20',
            'unsigned'   => true,
            'sortable'   => true,
            'allow_null' => true,
            'default'    => null,
            'validate'   => '\\Book_Database\\BerlinDB\\Sanitization\\absint_allow_null'
        ),

        // price
        array(
            'name'       => 'price',
            'type'       => 'decimal',
            'length'     => '10, 2',
            'unsigned'   => true,
            'sortable'   => true,
            'allow_null' => true,
            'default'    => null,
            'validate'   => '\\SpecialEditions\\Utils\\Sanitizer::price'
        ),

        // currency_code
        array(
            'name'     => 'currency_code',
            'type'     => 'char',
            'length'   => '3',
            'sortable' => true,
            'validate' => 'sanitize_text_field'
        ),

        // available
        array(
            'name'     => 'available',
            'type'     => 'tinyint',
            'length'   => '1',
            'unsigned' => true,
            'sortable' => true,
            'default'  => 0,
            'validate' => 'absint'
        ),

        // url
        array(
            'name'     => 'url',
            'type'     => 'mediumtext',
            'validate' => 'sanitize_text_field'
        ),

        // description
        array(
            'name'       => 'description',
            'type'       => 'longtext',
            'searchable' => true,
            'validate'   => 'wp_kses_post'
        ),

        // date_created
        array(
            'name'       => 'date_created',
            'type'       => 'datetime',
            'default'    => '', // True default is current time, set in query class
            'created'    => true,
            'date_query' => true,
            'sortable'   => true,
        ),

        // date_modified
        array(
            'name'       => 'date_modified',
            'type'       => 'datetime',
            'default'    => '', // True default is current time, set in query class
            'modified'   => true,
            'date_query' => true,
            'sortable'   => true,
        ),

    );

}