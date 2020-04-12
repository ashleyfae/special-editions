<?php
/**
 * Admin Bar
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\AdminBar;

use SpecialEditions\Editions\Database;
use SpecialEditions\Utils\Pages;
use function Book_Database\user_can_edit_books;

/**
 * Adds admin bar nodes
 *
 * @param \WP_Admin_Bar $wp_admin_bar
 */
function addNodes( $wp_admin_bar ) {
    /**
     * Edit Edition
     */
    if ( get_query_var( 'special_edition_id' ) && user_can_edit_books() ) {
        try {
            $edition = Database::retrieve( get_query_var( 'special_edition_id' ) );

            $wp_admin_bar->add_node( array(
                'id'    => 'special_edition',
                'title' => __( 'Edit Special Edition', 'special-editions' ),
                'href'  => Pages::getAdminPage( array(
                    'view'       => 'edit',
                    'edition_id' => $edition->get_id()
                ) )
            ) );

            $wp_admin_bar->add_node( array(
                'id'    => 'edit_bdb_book',
                'title' => __( 'Edit Book', 'special-editions' ),
                'href'  => add_query_arg( array(
                    'page'    => 'bdb-books',
                    'view'    => 'edit',
                    'book_id' => urlencode( $edition->getBookID() )
                ), admin_url( 'admin.php' ) )
            ) );
        } catch ( \Exception $e ) {

        }

        $wp_admin_bar->remove_node( 'edit' );
    }
}

add_action( 'admin_bar_menu', __NAMESPACE__ . '\addNodes', 100 );