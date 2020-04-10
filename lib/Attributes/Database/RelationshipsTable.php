<?php
/**
 * RelationshipsTable.php
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Attributes\Database;

/**
 * Class RelationshipsTable
 *
 * @package SpecialEditions\Attributes\Database
 */
class RelationshipsTable extends \Book_Database\BerlinDB\Database\Table {

    /**
     * @var string Table name
     */
    protected $name = 'special_edition_attribute_relationships';

    /**
     * @var int Table version in format {YYYY}{MM}{DD}{1}
     */
    protected $version = 202004091;

    /**
     * @var array Upgrades to perform
     */
    protected $upgrades = array();

    /**
     * @inheritDoc
     */
    protected function set_schema() {
        $this->schema = "id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			attribute_id bigint(20) UNSIGNED NOT NULL,
			edition_id bigint(20) UNSIGNED NOT NULL,
			date_created datetime NOT NULL,
			date_modified datetime NOT NULL,
			INDEX attribute_id (attribute_id),
			INDEX edition_id (edition_id)";
    }

}