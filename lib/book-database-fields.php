<?php
/**
 * book-database-fields.php
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\BookDatabase;

use SpecialEditions\Editions\Database;
use SpecialEditions\Utils\Currency;
use function Book_Database\get_book_cover_image_sizes;
use function Book_Database\get_enabled_book_fields;

function registerFields( $fields ) {

    $fields['special_edition_price'] = array(
        'name'        => __( 'Special Edition Price', 'special-editions' ),
        'placeholder' => '[special_edition_price]',
        'label'       => sprintf( __( '<strong>Price:</strong> %s', 'special-editions' ), '[special_edition_price]' ),
    );

    $fields['special_edition_availability'] = array(
        'name'        => __( 'Special Edition Availability', 'special-editions' ),
        'placeholder' => '[special_edition_availability]',
        'label'       => '&nbsp;[special_edition_availability]',
        'linebreak'   => 'on'
    );

    $fields['special_edition_purchase_url'] = array(
        'name'        => __( 'Special Edition Purchase', 'special-editions' ),
        'placeholder' => '[special_edition_purchase_url]',
        'label'       => sprintf( '<a href="%s" class="btn btn-primary" target="_blank">' . __( 'Buy the Book', 'special-editions' ) . '</a>', '[special_edition_purchase_url]' ),
        'linebreak'   => 'on'
    );

    $fields['special_edition_attributes'] = array(
        'name'        => __( 'Special Edition Attributes', 'special-editions' ),
        'placeholder' => '[special_edition_attributes]',
        'label'       => '<p>[special_edition_attributes]</p>',
        'linebreak'   => 'on'
    );

    $fields['special_edition_description'] = array(
        'name'        => __( 'Special Edition Description', 'special-editions' ),
        'placeholder' => '[special_edition_description]',
        'label'       => '<div class="special-edition-description"><h2>Special Edition Details</h2>[special_edition_description]</div>',
    );

    return $fields;

}

add_filter( 'book-database/book/available-fields', __NAMESPACE__ . '\registerFields' );

/**
 * Image
 *
 * Override the BDB cover image with the edition one
 *
 * @param mixed                      $value  Final value to be included in the layout.
 * @param string                     $field  Field key.
 * @param \Book_Database\Book        $book   Book object.
 * @param \Book_Database\Book_Layout $layout Layout object.
 *
 * @return mixed
 */
function editionInfo( $value, $field, $book, $layout ) {

    $edition_id = get_query_var( 'special_edition_id' );

    if ( empty( $edition_id ) ) {
        return $value;
    }

    try {

        $edition = Database::retrieve( absint( $edition_id ) );

        // If we have an image, then immediately override it.
        if ( $edition->hasImage() ) {
            $enabled_fields = get_enabled_book_fields();

            $alignment = $enabled_fields['cover']['alignment'] ?? $this->fields['cover']['alignment'];
            $size      = $enabled_fields['cover']['size'] ?? $this->fields['cover']['size'];

            // Validate the size.
            if ( ! array_key_exists( $size, get_book_cover_image_sizes() ) ) {
                $size = 'full';
            }

            $class = 'align' . sanitize_html_class( $alignment );
            $value = '<img src="' . esc_url( $edition->getImageURL( $size ) ) . '" alt="' . esc_attr( wp_strip_all_tags( $book->get_title() ) ) . '" class="' . esc_attr( $class ) . '">';
        }

    } catch ( \Exception $e ) {

    }

    return $value;

}

add_filter( 'book-database/book/formatted-info/value/cover', __NAMESPACE__ . '\editionInfo', 10, 4 );

/**
 * Price
 *
 * @param mixed                      $value  Final value to be included in the layout.
 * @param string                     $field  Field key.
 * @param \Book_Database\Book        $book   Book object.
 * @param \Book_Database\Book_Layout $layout Layout object.
 *
 * @return mixed
 */
function price( $value, $field, $book, $layout ) {

    $edition_id = get_query_var( 'special_edition_id' );

    if ( empty( $edition_id ) ) {
        return $value;
    }

    try {
        $edition = Database::retrieve( absint( $edition_id ) );

        if ( $edition->hasPrice() ) {
            $value = Currency::formatAmount( $edition->getPrice(), $edition->getCurrencyCode() );
        }
    } catch ( \Exception $e ) {

    }

    return $value;

}

add_filter( 'book-database/book/formatted-info/value/special_edition_price', __NAMESPACE__ . '\price', 10, 4 );

/**
 * Price
 *
 * @param mixed                      $value  Final value to be included in the layout.
 * @param string                     $field  Field key.
 * @param \Book_Database\Book        $book   Book object.
 * @param \Book_Database\Book_Layout $layout Layout object.
 *
 * @return mixed
 */
function availability( $value, $field, $book, $layout ) {

    $edition_id = get_query_var( 'special_edition_id' );

    if ( empty( $edition_id ) ) {
        return $value;
    }

    try {
        $edition = Database::retrieve( absint( $edition_id ) );

        if ( $edition->isAvailable() ) {
            $value = '<span class="badge badge-success">' . __( 'Available', 'special-editions' ) . '</span>';
        } else {
            $value = '<span class="badge badge-danger">' . __( 'Unavailable', 'special-editions' ) . '</span>';
        }
    } catch ( \Exception $e ) {

    }

    return $value;

}

add_filter( 'book-database/book/formatted-info/value/special_edition_availability', __NAMESPACE__ . '\availability', 10, 4 );

/**
 * Purchase
 *
 * @param mixed                      $value  Final value to be included in the layout.
 * @param string                     $field  Field key.
 * @param \Book_Database\Book        $book   Book object.
 * @param \Book_Database\Book_Layout $layout Layout object.
 *
 * @return mixed
 */
function purchase( $value, $field, $book, $layout ) {

    $edition_id = get_query_var( 'special_edition_id' );

    if ( empty( $edition_id ) ) {
        return $value;
    }

    try {
        $edition = Database::retrieve( absint( $edition_id ) );
        $value   = $edition->getURL();
    } catch ( \Exception $e ) {

    }

    return $value;

}

add_filter( 'book-database/book/formatted-info/value/special_edition_purchase_url', __NAMESPACE__ . '\purchase', 10, 4 );

/**
 * Attributes
 *
 * @param mixed                      $value  Final value to be included in the layout.
 * @param string                     $field  Field key.
 * @param \Book_Database\Book        $book   Book object.
 * @param \Book_Database\Book_Layout $layout Layout object.
 *
 * @return mixed
 */
function attributes( $value, $field, $book, $layout ) {

    $edition_id = get_query_var( 'special_edition_id' );

    if ( empty( $edition_id ) ) {
        return $value;
    }

    try {
        $edition    = Database::retrieve( absint( $edition_id ) );
        $attributes = $edition->getAttributes( array( 'fields' => 'names' ) );

        if ( $attributes ) {
            foreach ( $attributes as $attribute ) {
                $value .= '<span class="badge badge-pill badge-secondary">' . esc_html( $attribute ) . '</span> ';
            }
        }
    } catch ( \Exception $e ) {

    }

    return $value;

}

add_filter( 'book-database/book/formatted-info/value/special_edition_attributes', __NAMESPACE__ . '\attributes', 10, 4 );

/**
 * Description
 *
 * @param mixed                      $value  Final value to be included in the layout.
 * @param string                     $field  Field key.
 * @param \Book_Database\Book        $book   Book object.
 * @param \Book_Database\Book_Layout $layout Layout object.
 *
 * @return mixed
 */
function description( $value, $field, $book, $layout ) {

    $edition_id = get_query_var( 'special_edition_id' );

    if ( empty( $edition_id ) ) {
        return $value;
    }

    try {
        $edition = Database::retrieve( absint( $edition_id ) );

        if ( $edition->getDescription() ) {
            $value = wpautop( $edition->getDescription() );
        }
    } catch ( \Exception $e ) {

    }

    return $value;

}

add_filter( 'book-database/book/formatted-info/value/special_edition_description', __NAMESPACE__ . '\description', 10, 4 );