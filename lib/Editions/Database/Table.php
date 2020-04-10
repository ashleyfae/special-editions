<?php
/**
 * Table
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Editions\Database;

/**
 * Class Table
 *
 * @package SpecialEditions\Editions\Database
 */
class Table extends \Book_Database\BerlinDB\Database\Table {

    /**
     * @var string Table name
     */
    protected $name = 'special_editions';

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
			book_id bigint(20) UNSIGNED NOT NULL,
			image_id bigint(20) UNSIGNED DEFAULT NULL,
			price decimal(10,2) DEFAULT NULL,
			currency_code char(3) DEFAULT NULL,
			available tinyint(1) NOT NULL DEFAULT 0,
			url mediumtext NOT NULL DEFAULT '',
			description longtext NOT NULL,
			date_created datetime NOT NULL,
			date_modified datetime NOT NULL,
			INDEX book_id_available (book_id, available)";
    }

}