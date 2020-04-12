<?php
/**
 * actions.php
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Editions;

use SpecialEditions\Utils\Cache;

/**
 * Purge Varnish cache when editions are added/updated
 *
 * @param Edition $edition
 */
function purgeCache( $edition ) {

    $urls = array(
        home_url( '/' ),
        home_url( '/special-editions/' ),
        home_url( '/special-edition/' . urlencode( $edition->get_id() ) . '/' )
    );

    foreach ( $urls as $url ) {
        Cache::purgeURL( $url );
    }

}

add_action( 'special-editions/edition-added', __NAMESPACE__ . '\purgeCache' );
add_action( 'special-editions/edition-updated', __NAMESPACE__ . '\purgeCache' );