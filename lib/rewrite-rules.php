<?php
/**
 * rewrite-rules.php
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions;

/**
 * Adds rewrite rules
 */
function addRules() {

    if ( ! defined( 'SPECIAL_EDITION_PAGE_ID' ) ) {
        return;
    }

    add_rewrite_tag( '%special_edition_id%', '([^&]+)' );

    add_rewrite_rule( '^special-edition/([^/]*)/?', 'index.php?page_id=' . SPECIAL_EDITION_PAGE_ID . '&special_edition_id=$matches[1]', 'top' );
    //flush_rewrite_rules( true );

}

add_action( 'init', __NAMESPACE__ . '\addRules' );

function addQueryVar( $vars ) {
    $vars[] = 'special_edition_id';

    return $vars;
}

add_filter( 'query_vars', __NAMESPACE__ . '\addQueryVar' );