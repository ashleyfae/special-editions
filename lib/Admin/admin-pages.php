<?php
/**
 * Admin Pages
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Admin;

/**
 * Registers admin pages
 */
function register_pages() {

    global $bdb_special_edition_admin_pages;

    $bdb_special_edition_admin_pages['editions'] = add_submenu_page( 'bdb-books', __( 'Special Editions', 'special-editions' ), __( 'Special Editions', 'special-editions' ), 'bdb_edit_books', 'bdb-special-editions', __NAMESPACE__ . '\render_page' );

    // Now hide this page...
    add_action( 'admin_head', __NAMESPACE__ . '\hide_page' );

}

add_action( 'admin_menu', __NAMESPACE__ . '\register_pages' );

/**
 * Hides the page from the admin menu
 */
function hide_page() {
    remove_submenu_page( 'bdb-books', 'bdb-special-editions' );
}

/**
 * Registers Special Edition pages as BDB admin pages
 *
 * @param bool       $is_admin_page
 * @param \WP_Screen $screen
 *
 * @return bool
 */
function register_bdb_page( $is_admin_page, $screen ) {

    global $bdb_special_edition_admin_pages;

    if ( in_array( $screen->id, $bdb_special_edition_admin_pages ) ) {
        $is_admin_page = true;
    }

    return $is_admin_page;

}

add_filter( 'book-database/is-admin-page', __NAMESPACE__ . '\register_bdb_page', 10, 2 );

/**
 * Renders the special editions page
 */
function render_page() {

    $view = ! empty( $_GET['view'] ) ? urldecode( $_GET['view'] ) : '';

    switch ( $view ) {

        case 'add' :
        case 'edit' :
            require_once 'edition-fields.php';
            require_once 'Views/edit-special-edition.php';
            break;

        default :
            wp_die( __( 'Are you lost?', 'special-edition' ) );
            break;

    }

}