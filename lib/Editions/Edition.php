<?php
/**
 * Edition
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Editions;

use SpecialEditions\Utils\Currency;

/**
 * Class Edition
 *
 * @package SpecialEditions\Editions
 */
class Edition extends \Book_Database\Base_Object {

    /**
     * @var int
     */
    protected $book_id = 0;

    /**
     * @var int|null
     */
    protected $image_id = null;

    /**
     * @var float|null
     */
    protected $price = null;

    /**
     * @var string|null
     */
    protected $currency_code = null;

    /**
     * @var int
     */
    protected $available = 0;

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * Retrieves the ID of the book
     *
     * @return int
     */
    public function getBookID() {
        return absint( $this->book_id );
    }

    /**
     * Retrieves the ID of the image
     *
     * @return int|null
     */
    public function getImageID() {
        return ! empty( $this->image_id ) ? absint( $this->image_id ) : null;
    }

    /**
     * Determines whether or not the edition has an image
     *
     * @return bool
     */
    public function hasImage() {
        return ! empty( $this->image_id );
    }

    /**
     * Returns the URL for the image
     *
     * @param string $size
     *
     * @return string
     */
    public function getImageURL( $size = 'full' ) {
        return $this->hasImage() ? wp_get_attachment_image_url( $this->getImageID(), $size ) : '';
    }

    /**
     * Renders the associated image
     *
     * @param string $size       Desired image size.
     * @param array  $attributes Image attributes.
     *
     * @return void
     */
    public function displayImage( $size = 'full', $attributes = array() ) {
        echo wp_get_attachment_image( $this->getImageID(), $size, false, $attributes );
    }

    /**
     * Returns the price
     *
     * @return float|null
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * Returns the currency code
     *
     * @return string|null
     */
    public function getCurrencyCode() {
        return $this->currency_code;
    }

    /**
     * Determines whether or not a price has been set
     *
     * @return bool
     */
    public function hasPrice() {
        return ! is_null( $this->getPrice() ) && ! is_null( $this->getCurrencyCode() );
    }

    /**
     * Prints the display-ready price
     */
    public function displayPrice() {
        if ( $this->hasPrice() ) {
            echo Currency::formatAmount( $this->getPrice(), $this->getCurrencyCode() );
        }
    }

    /**
     * Determines whether or not the edition is still available for sale
     *
     * @return bool
     */
    public function isAvailable() {
        return 1 === absint( $this->available );
    }

    /**
     * Retrieves the purchase URL
     *
     * @return string
     */
    public function getURL() {
        return $this->url;
    }

    /**
     * Retrieves the description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Retrieves the edition's attributes
     *
     * @param array $args
     *
     * @return \SpecialEditions\Attributes\Attribute[]|array
     */
    public function getAttributes( $args = array() ) {
        return \SpecialEditions\Attributes\Database::retrieveAttachedAttributes( $this->get_id(), $args );
    }

}