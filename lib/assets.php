<?php
/**
 * assets.php
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions;

function loadAssets() {
    $version = filemtime( SPECIAL_EDITIONS_ROOT_DIR . 'assets/css/front-end.css' );
    wp_enqueue_style( 'special-editions', SPECIAL_EDITIONS_ROOT_URL . 'assets/css/front-end.css', array(), $version );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\loadAssets' );