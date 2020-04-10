<?php
/**
 * RelationshipsDatabase.php
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Attributes;

use Book_Database\BerlinDB\Database\Row;

/**
 * Class RelationshipsDatabase
 *
 * @package SpecialEditions\Attributes
 */
class RelationshipsDatabase extends \SpecialEditions\Database {

    /**
     * @inheritDoc
     */
    public static function getQuery( $args = array() ) {
        return new Database\RelationshipsQuery();
    }

    /**
     * Retrieves an attribute-edition relationship
     *
     * @param int $attribute_id
     * @param int $edition_id
     *
     * @return Row
     * @throws \Exception
     */
    public static function retrieveByAttributeAndEdition( $attribute_id, $edition_id ) {

        $relationships = self::query( array(
            'number'       => 1,
            'attribute_id' => absint( $attribute_id ),
            'edition_id'   => absint( $edition_id )
        ) );

        if ( empty( $relationships ) ) {
            throw new \Exception( __( 'No relationship found.', 'special-editions' ) );
        }

        return reset( $relationships );

    }
}