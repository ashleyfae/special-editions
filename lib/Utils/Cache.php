<?php
/**
 * Cache.php
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Utils;

/**
 * Class Cache
 *
 * @package SpecialEditions\Utils
 */
class Cache {

    /**
     * Purge the cache
     *
     * @param $url
     */
    public static function purgeURL( $url ) {
        wp_remote_request( $url, array(
            'method' => 'PURGE'
        ) );
    }

    /**
     * Purge the cache for the entire site
     */
    public static function purgeSite() {
        wp_remote_request( home_url(), array(
            'method' => 'BAN'
        ) );
    }

}