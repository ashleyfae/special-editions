<?php
/**
 * Admin Book Actions
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Admin;

use Book_Database\Book;
use SpecialEditions\Editions\Database;
use SpecialEditions\Utils\Pages;
use function Book_Database\user_can_edit_books;

/**
 * Render special edition fields
 *
 * @param Book|false $book
 */
function renderEditionFields( $book ) {

    if ( ! $book instanceof Book ) {
        return;
    }

    $editions        = Database::query( array(
        'book_id' => $book->get_id(),
        'number'  => 999
    ) );
    $add_edition_url = Pages::getAdminPage( array( 'view' => 'add', 'book_id' => $book->get_id() ) );
    ?>
    <div id="bdb-book-special-editions-list" class="postbox">
        <h2><?php _e( 'Special Editions', 'special-editions' ); ?></h2>

        <div class="inside">
            <?php if ( ! empty( $editions ) ) : ?>
                <div id="bdb-book-special-editions-grid">
                    <?php foreach ( $editions as $edition ) :
                        $edit_url = Pages::getAdminPage( array(
                            'view'       => 'edit',
                            'edition_id' => $edition->get_id()
                        ) );
                        ?>
                        <div class="bdb-book-special-edition">
                            <?php
                            if ( $edition->hasImage() ) {
                                echo '<a href="' . esc_url( $edit_url ) . '">';
                                $edition->displayImage( 'large' );
                                echo '</a>';
                            } else {
                                // @todo default image
                            }
                            ?>

                            <p>
                                <a href="<?php echo esc_url( $edit_url ); ?>" class="button"><?php _e( 'Edit Edition', 'special-editions' ); ?></a>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <a href="<?php echo esc_url( $add_edition_url ); ?>" class="button"><?php _e( 'Add Special Edition', 'special-editions' ); ?></a>
        </div>
    </div>
    <?php

}

add_action( 'book-database/book-edit/after-information-fields', __NAMESPACE__ . '\renderEditionFields' );

/**
 * Add a new edition
 */
function addEdition() {

    if ( empty( $_POST['special_editions_add_edition_nonce'] ) ) {
        return;
    }

    try {

        if ( ! wp_verify_nonce( $_POST['special_editions_add_edition_nonce'], 'special_editions_add_edition' ) || ! user_can_edit_books() ) {
            throw new \Exception( __( 'You do not have permission to perform this action.', 'special-editions' ), 403 );
        }

        if ( empty( $_POST['book_id'] ) ) {
            throw new \Exception( __( 'Missing book ID', 'special-editions' ), 400 );
        }

        $edition = Database::create( array(
            'book_id'       => $_POST['book_id'],
            'image_id'      => ! empty( $_POST['image_id'] ) ? $_POST['image_id'] : null,
            'price'         => ! empty( $_POST['price'] ) ? $_POST['price'] : null,
            'currency_code' => ! empty( $_POST['currency_code'] ) ? $_POST['currency_code'] : null,
            'available'     => ! empty( $_POST['available'] ) ? 1 : 0,
            'url'           => ! empty( $_POST['url'] ) ? esc_url_raw( $_POST['url'] ) : '',
            'description'   => $_POST['description'] ?? ''
        ) );

        /*
         * Set the attributes
         */
        $attributes      = ! empty( $_POST['attributes'] ) && is_array( $_POST['attributes'] ) ? stripslashes_deep( $_POST['attributes'] ) : array();
        $attribute_names = array_unique( array_map( 'trim', $attributes ) );

        \SpecialEditions\Attributes\Database::setAttributes( $edition->get_id(), $attribute_names );

        $edit_url = Pages::getAdminPage( array(
            'view'                   => 'edit',
            'edition_id'             => $edition->get_id(),
            'special_edition_notice' => 'edition_added'
        ) );

        wp_safe_redirect( esc_url_raw( $edit_url ) );
        exit;

    } catch ( \Exception $e ) {
        wp_die( $e->getMessage() );
    }

}

add_action( 'admin_init', __NAMESPACE__ . '\addEdition' );

/**
 * Update an edition
 */
function updateEdition() {

    if ( empty( $_POST['special_editions_update_edition_nonce'] ) ) {
        return;
    }

    try {

        if ( ! wp_verify_nonce( $_POST['special_editions_update_edition_nonce'], 'special_editions_update_edition' ) || ! user_can_edit_books() ) {
            throw new \Exception( __( 'You do not have permission to perform this action.', 'special-editions' ), 403 );
        }

        if ( empty( $_POST['edition_id'] ) ) {
            throw new \Exception( __( 'Missing edition ID', 'special-editions' ), 400 );
        }

        Database::update( absint( $_POST['edition_id'] ), array(
            'image_id'      => ! empty( $_POST['image_id'] ) ? $_POST['image_id'] : null,
            'price'         => ! empty( $_POST['price'] ) ? $_POST['price'] : null,
            'currency_code' => ! empty( $_POST['currency_code'] ) ? $_POST['currency_code'] : null,
            'available'     => ! empty( $_POST['available'] ) ? 1 : 0,
            'url'           => ! empty( $_POST['url'] ) ? esc_url_raw( $_POST['url'] ) : '',
            'description'   => $_POST['description'] ?? ''
        ) );

        /*
         * Set the attributes
         */
        $attributes      = ! empty( $_POST['attributes'] ) && is_array( $_POST['attributes'] ) ? stripslashes_deep( $_POST['attributes'] ) : array();
        $attribute_names = array_unique( array_map( 'trim', $attributes ) );

        \SpecialEditions\Attributes\Database::setAttributes( absint( $_POST['edition_id'] ), $attribute_names );

        $edit_url = Pages::getAdminPage( array(
            'view'                   => 'edit',
            'edition_id'             => absint( $_POST['edition_id'] ),
            'special_edition_notice' => 'edition_updated'
        ) );

        wp_safe_redirect( esc_url_raw( $edit_url ) );
        exit;

    } catch ( \Exception $e ) {
        wp_die( $e->getMessage() );
    }

}

add_action( 'admin_init', __NAMESPACE__ . '\updateEdition' );