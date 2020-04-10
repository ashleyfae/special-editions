<?php
/**
 * Currency.php
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Utils;


/**
 * Class Currency
 *
 * @package SpecialEditions\Utils
 */
class Currency {

    /**
     * Returns all supported currencies
     *
     * @return array
     */
    public static function getCurrencies() {
        return array(
            'GBP',
            'USD'
        );
    }

    /**
     * Retrieves the symbol for a currency code
     *
     * @param string $code Currency code
     *
     * @return string
     */
    public static function getSymbol( $code ) {
        switch ( $code ) {
            case 'USD' :
                return '$';
                break;

            case 'GBP' :
                return '£';
                break;

            default :
                return '';
        }
    }

    public static function formatAmount( $price, $currency_code ) {
        return sprintf(
            '%s%s %s',
            self::getSymbol( $currency_code ),
            $price,
            $currency_code
        );
    }

}