<?php
/**
 * AdminBar.php
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions;


class AdminBar {

    /**
     * @param \WP_Admin_Bar $wp_admin_bar
     */
    public static function addNodes( $wp_admin_bar ) {

        if ( get_query_var( 'edition_id' ) ) {
            $wp_admin_bar->add_node(array(
                'id' => 'special_edition',
                'title' => __('Edit Special Edition', 'special-editions')
            ));
        }

    }

}

add_action( 'admin_bar_menu', 'AdminBar::addNodes' );