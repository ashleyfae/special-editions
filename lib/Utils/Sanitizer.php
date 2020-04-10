<?php
/**
 * Sanitizer
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Utils;

/**
 * Class Sanitizer
 *
 * @package SpecialEditions\Utils
 */
class Sanitizer {

    public static function price( $price ) {

        if ( is_null( $price ) ) {
            return null;
        }

        return round( floatval( $price ), 2 );

    }

}