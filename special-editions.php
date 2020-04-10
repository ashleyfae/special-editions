<?php
/**
 * Plugin Name: Special Editions
 * Description: Keep track of special editions.
 * Version: 1.0
 * Author: Ashley Gibson
 * Author URI: https://www.nosegraze.com
 * License: GPL2+
 *
 * Special Editions is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Special Editions is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Special Editions. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions;

/**
 * Class Plugin
 *
 * @package SpecialEditions
 */
final class Plugin {

    /**
     * Plugin instance
     *
     * @var Plugin|\WP_Error|null
     */
    private static $instance = null;

    /**
     * Initialize
     *
     * @return self
     */
    public static function init() {

        if ( ! isset( self::$instance ) ) {
            if ( ! class_exists( 'Book_Database\\Book_Database' ) ) {
                self::$instance = new \WP_Error( 'not_supported', __( 'Not supported.', 'special-editions' ) );

                return self::$instance;
            }

            self::$instance = new self;
            self::$instance->_defineConstants();
            self::$instance->_setupAutoloader();
            self::$instance->_includeFunctions();
            self::$instance->_setupApplication();
        }

        return self::$instance;

    }

    /**
     * Defines constants
     *
     * @since 1.0
     */
    private function _defineConstants() {

        define( 'SPECIAL_EDITIONS_ROOT_DIR', plugin_dir_path( __FILE__ ) );
        define( 'SPECIAL_EDITIONS_ROOT_URL', plugin_dir_url( __FILE__ ) );

    }

    /**
     * Registers the autoloader
     *
     * @since 1.0
     * @return bool
     */
    private function _setupAutoloader() {

        try {

            spl_autoload_register( function ( $class ) {
                $class = explode( '\\', $class );

                if ( __NAMESPACE__ === $class[0] ) {
                    array_shift( $class );
                }

                $file_name = array_pop( $class );
                $directory = implode( DIRECTORY_SEPARATOR, $class );
                $file      = trailingslashit( SPECIAL_EDITIONS_ROOT_DIR ) . 'lib/' . $directory . '/' . $file_name . '.php';

                if ( file_exists( $file ) ) {
                    require $file;

                    return true;
                }

                return false;
            } );

        } catch ( \Exception $e ) {
        }

        return false;

    }

    /**
     * Includes function files
     *
     * @since 1.0
     * @return void
     */
    private function _includeFunctions() {

        if ( is_admin() ) {
            require_once SPECIAL_EDITIONS_ROOT_DIR . 'lib/Admin/admin-pages.php';
            require_once SPECIAL_EDITIONS_ROOT_DIR . 'lib/Admin/book-actions.php';
        } else {
            require_once SPECIAL_EDITIONS_ROOT_DIR . 'lib/assets.php';
            require_once SPECIAL_EDITIONS_ROOT_DIR . 'lib/content-filters.php';
            require_once SPECIAL_EDITIONS_ROOT_DIR . 'lib/rewrite-rules.php';
            require_once SPECIAL_EDITIONS_ROOT_DIR . 'lib/shortcodes.php';
        }

        require_once SPECIAL_EDITIONS_ROOT_DIR . 'lib/book-database-fields.php';

    }

    /**
     * Sets up the application
     *
     *      - Installs database tables.
     *
     * @since 1.0
     */
    private function _setupApplication() {
        new Editions\Database\Table();
        new Attributes\Database\Table();
        new Attributes\Database\RelationshipsTable();
    }

}

add_action( 'plugins_loaded', __NAMESPACE__ . '\Plugin::init' );