<?php
/**
 * Pages.php
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Utils;

/**
 * Class Pages
 *
 * @package SpecialEditions\Utils
 */
class Pages {

    /**
     * Returns the URL to the special editions admin page
     *
     * @param array $args
     *
     * @return string
     */
    public static function getAdminPage( $args = array() ) {

        $args = wp_parse_args( $args, array(
            'page' => 'bdb-special-editions'
        ) );

        $sanitized_args = array();

        foreach ( $args as $key => $value ) {
            $sanitized_args[ urlencode( $key ) ] = urlencode( $value );
        }

        return add_query_arg( $sanitized_args, admin_url( 'admin.php' ) );

    }

    /**
     * Returns the URL for deleting a special edition
     *
     * @param int $edition_id
     *
     * @return string
     */
    public static function getDeleteEditionURL( $edition_id ) {
        return wp_nonce_url( self::getAdminPage( array(
            'edition_id' => $edition_id
        ) ), 'bdb_delete_special_edition' );
    }

}