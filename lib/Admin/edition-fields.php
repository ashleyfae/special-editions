<?php
/**
 * Fields
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Admin\Fields;

use SpecialEditions\Attributes\Database;
use SpecialEditions\Editions\Edition;
use SpecialEditions\Utils\Currency;
use function Book_Database\book_database;

/**
 * Image field
 *
 * @param Edition|false $edition
 */
function image( $edition ) {

    $cover_id  = $edition instanceof Edition ? $edition->getImageID() : null;
    $cover_url = $edition instanceof Edition ? $edition->getImageURL( 'medium' ) : '';

    ob_start();
    ?>
    <img src="<?php echo esc_url( $cover_url ); ?>" alt="<?php esc_attr_e( 'Book cover', 'special-editions' ); ?>" id="bdb-cover-image" style="<?php echo empty( $cover_url ) ? 'display: none;' : ''; ?>">

    <div class="bdb-cover-image-fields" data-image="#bdb-cover-image" data-image-id="#edition-image-id" data-image-size="large">
        <button type="button" class="bdb-upload-image button"><?php esc_html_e( 'Upload Image', 'special-editions' ); ?></button>
        <button type="button" class="bdb-remove-image button" style="<?php echo empty( $cover_id ) ? 'display: none;' : ''; ?>"><?php esc_html_e( 'Remove Image', 'special-editions' ); ?></button>
    </div>
    <input type="hidden" id="edition-image-id" name="image_id" value="<?php echo esc_attr( $cover_id ); ?>">
    <?php

    book_database()->get_html()->meta_row( array(
        'label' => __( 'Cover Image', 'special-editions' ),
        'field' => ob_get_clean()
    ) );

}

add_action( 'special-editions/edit/fields', __NAMESPACE__ . '\image' );

/**
 * Price field
 *
 * @param Edition|false $edition
 */
function price( $edition ) {

    $price    = $edition instanceof Edition ? $edition->getPrice() : null;
    $currency = $edition instanceof Edition ? $edition->getCurrencyCode() : null;

    ob_start();
    ?>
    <label for="edition-currency-code" class="screen-reader-text"><?php _e( 'Currency', 'special-editions' ); ?></label>
    <select id="edition-currency-code" name="currency_code">
        <?php foreach ( Currency::getCurrencies() as $currency_code ) : ?>
            <option value="<?php echo esc_attr( $currency_code ); ?>" <?php selected( $currency_code, $currency ); ?>><?php echo esc_html( $currency_code ); ?></option>
        <?php endforeach; ?>
    </select>

    <label for="edition-price" class="screen-reader-text"><?php _e( 'Price', 'special-editions' ); ?></label>
    <input type="text" id="edition-price" class="text-small" name="price" value="<?php echo ! is_null( $price ) ? esc_attr( $price ) : ''; ?>">
    <?php
    book_database()->get_html()->meta_row( array(
        'label' => __( 'Price', 'special-editions' ),
        'id'    => 'edition-price',
        'field' => ob_get_clean()
    ) );

}

add_action( 'special-editions/edit/fields', __NAMESPACE__ . '\price' );

/**
 * Available field
 *
 * @param Edition|false $edition
 */
function available( $edition ) {

    $available = $edition instanceof Edition ? $edition->isAvailable() : false;

    ob_start();
    ?>
    <label for="edition-available">
        <input type="checkbox" id="edition-available" name="available" value="1" <?php checked( $available ); ?>>
        <?php _e( 'Available for sale', 'special-editions' ); ?>
    </label>
    <?php
    book_database()->get_html()->meta_row( array(
        'label' => __( 'Availability', 'special-editions' ),
        'id'    => 'edition-available',
        'field' => ob_get_clean()
    ) );

}

add_action( 'special-editions/edit/fields', __NAMESPACE__ . '\available' );

/**
 * URL field
 *
 * @param Edition|false $edition
 */
function url( $edition ) {

    $url = $edition instanceof Edition ? $edition->getURL() : '';

    ob_start();
    ?>
    <input type="text" id="edition-url" class="regular-text" name="url" value="<?php echo esc_attr( $url ); ?>">
    <?php
    book_database()->get_html()->meta_row( array(
        'label' => __( 'Purchase URL', 'special-editions' ),
        'id'    => 'edition-url',
        'field' => ob_get_clean()
    ) );

}

add_action( 'special-editions/edit/fields', __NAMESPACE__ . '\url' );

/**
 * Description field
 *
 * @param Edition|false $edition
 */
function description( $edition ) {

    $description = $edition instanceof Edition ? $edition->getDescription() : '';

    ob_start();
    ?>
    <textarea id="edition-description" class="large-textarea" name="description"><?php echo esc_textarea( $description ); ?></textarea>
    <?php
    book_database()->get_html()->meta_row( array(
        'label' => __( 'Description', 'special-editions' ),
        'id'    => 'edition-description',
        'field' => ob_get_clean()
    ) );

}

add_action( 'special-editions/edit/fields', __NAMESPACE__ . '\description' );

/**
 * Attributes field
 *
 * @param Edition|false $edition
 */
function attributes( $edition ) {

    $selected_attributes = array();

    if ( $edition instanceof Edition ) {
        $selected_attributes = Database::retrieveAttachedAttributes( $edition->get_id(), array( 'fields' => 'names' ) );
    }

    // Get all the attributes except the ones already selected.
    $attributes = Database::query( array(
        'number'       => 300,
        'name__not_in' => $selected_attributes,
        'fields'       => 'name',
        'orderby'      => 'name',
        'order'        => 'ASC'
    ) );

    $final_attributes = $selected_attributes + $attributes;

    ob_start();
    ?>
    <div id="bdb-checkboxes-special-edition-attributes" class="bdb-taxonomy-checkboxes" data-taxonomy="attributes" data-name="attributes[]">
        <div class="bdb-checkbox-wrap">
            <?php
            foreach ( $final_attributes as $attribute_name ) {
                ?>
                <label for="<?php echo esc_attr( sanitize_html_class( sanitize_key( sprintf( 'special-edition-attribute-%s', $attribute_name ) ) ) ); ?>">
                    <input type="checkbox" id="<?php echo esc_attr( sanitize_html_class( sanitize_key( sprintf( 'special-edition-attribute-%s', $attribute_name ) ) ) ); ?>" class="bdb-checkbox" name="attributes[]" value="<?php echo esc_attr( $attribute_name ); ?>" <?php checked( in_array( $attribute_name, $selected_attributes ) ); ?>>
                    <?php echo esc_html( $attribute_name ); ?>
                </label>
                <?php
            }
            ?>
        </div>
        <div class="bdb-new-checkbox-term">
            <label for="bdb-new-checkbox-term-attribute" class="screen-reader-text"><?php esc_html_e( 'Enter the name of a new attribute', 'special-editions' ); ?></label>
            <input type="text" id="bdb-new-checkbox-term-attribute" class="regular-text bdb-new-checkbox-term-value">
            <input type="button" class="button" value="<?php esc_attr_e( 'Add', 'special-editions' ); ?>">
        </div>
    </div>
    <?php
    book_database()->get_html()->meta_row( array(
        'label' => __( 'Attributes', 'special-editions' ),
        'field' => ob_get_clean()
    ) );

}

add_action( 'special-editions/edit/fields', __NAMESPACE__ . '\attributes' );