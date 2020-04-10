<?php
/**
 * Query
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Editions\Database;

/**
 * Class Query
 *
 * @package SpecialEditions\Editions\Database
 */
class Query extends \Book_Database\BerlinDB\Database\Query {

    /**
     * Name of the table to query
     *
     * @var string
     */
    protected $table_name = 'special_editions';

    /**
     * String used to alias the database table in MySQL statements
     *
     * @var string
     */
    protected $table_alias = 'special_eds';

    /**
     * Name of class used to set up the database schema
     *
     * @var string
     */
    protected $table_schema = '\\SpecialEditions\\Editions\\Database\\Schema';

    /**
     * Name for a single item
     *
     * @var string
     */
    protected $item_name = 'special_edition';

    /**
     * Plural version for a group of items
     *
     * @var string
     */
    protected $item_name_plural = 'special_editions';

    /**
     * Class name to turn IDs into these objects
     *
     * @var string
     */
    protected $item_shape = '\\SpecialEditions\\Editions\\Edition';

    /**
     * Group to cache queries and queried items to
     *
     * @var string
     */
    protected $cache_group = 'special_editions';

}