<?php
/**
 * Table
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Attributes\Database;

/**
 * Class Table
 *
 * @package SpecialEditions\Attributes\Database
 */
class Table extends \Book_Database\BerlinDB\Database\Table {

    /**
     * @var string Table name
     */
    protected $name = 'special_edition_attributes';

    /**
     * @var int Table version in format {YYYY}{MM}{DD}{1}
     */
    protected $version = 202004071;

    /**
     * @var array Upgrades to perform
     */
    protected $upgrades = array();

    /**
     * @inheritDoc
     */
    protected function set_schema() {
        $this->schema = "id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			name varchar(155) NOT NULL,
			slug varchar(155) NOT NULL,
			description longtext NOT NULL,
			date_created datetime NOT NULL,
			date_modified datetime NOT NULL,
			UNIQUE INDEX name (name),
			UNIQUE INDEX slug (slug)";
    }

}