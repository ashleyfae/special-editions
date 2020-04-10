<?php
/**
 * Schema
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Attributes\Database;

/**
 * Class Schema
 *
 * @package SpecialEditions\Attributes\Database
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

        // name
        array(
            'name'       => 'name',
            'type'       => 'varchar',
            'length'     => '155',
            'sortable'   => true,
            'searchable' => true,
            'validate'   => 'sanitize_text_field'
        ),

        // slug
        array(
            'name'     => 'slug',
            'type'     => 'varchar',
            'length'   => '155',
            'sortable' => true,
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