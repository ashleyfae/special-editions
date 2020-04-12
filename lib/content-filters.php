<?php
/**
 * content-filters.php
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\ContentFilters;

use SpecialEditions\Editions\Database;

if ( ! defined( 'SPECIAL_EDITION_PAGE_ID' ) ) {
    return;
}

/**
 * Filters the `<title>` tag value
 *
 * @param array $title
 *
 * @return array
 */
function titleTag( $title ) {

    global $post;

    if ( ! $post instanceof \WP_Post ) {
        return $title;
    }

    if ( $post->ID != SPECIAL_EDITION_PAGE_ID ) {
        return $title;
    }

    $edition_id = get_query_var( 'special_edition_id' );

    if ( empty( $edition_id ) ) {
        return $title;
    }

    try {
        $edition        = Database::retrieve( absint( $edition_id ) );
        $book           = \Book_Database\get_book( $edition->getBookID() );
        $title['title'] = sprintf( __( 'Special Edition: %s by %s', 'special-editions' ), $book->get_title(), $book->get_author_names( true ) );
    } catch ( \Exception $e ) {
    }

    return $title;

}

add_filter( 'document_title_parts', __NAMESPACE__ . '\titleTag' );

/**
 * Filters the page title
 *
 * @param string $title
 * @param int    $post_id
 *
 * @return string
 */
function title( $title, $post_id ) {

    if ( $post_id != SPECIAL_EDITION_PAGE_ID ) {
        return $title;
    }

    $edition_id = get_query_var( 'special_edition_id' );

    if ( empty( $edition_id ) ) {
        return $title;
    }

    try {
        $edition = Database::retrieve( absint( $edition_id ) );
        $book    = \Book_Database\get_book( $edition->getBookID() );
        $title   = sprintf( __( '%s by %s', 'special-editions' ), $book->get_title(), $book->get_author_names( true ) );
    } catch ( \Exception $e ) {
    }

    return $title;

}

add_filter( 'the_title', __NAMESPACE__ . '\title', 10, 2 );

/**
 * Filters the page content
 *
 * @param string $content
 *
 * @return string
 */
function content( $content ) {

    $post_id = get_the_ID();

    if ( empty( $post_id ) || $post_id != SPECIAL_EDITION_PAGE_ID ) {
        return $content;
    }

    $edition_id = get_query_var( 'special_edition_id' );

    if ( empty( $edition_id ) ) {
        return $content;
    }

    try {
        $edition = Database::retrieve( absint( $edition_id ) );
        $book    = \Book_Database\get_book( $edition->getBookID() );
        $layout  = new \Book_Database\Book_Layout( $book );

        ob_start();

        if ( ! $edition->isAvailable() ) {
            ?>
            <div class="alert alert-danger" role="alert">
                <?php _e( 'This edition is no longer available.', 'special-editions' ); ?>
            </div>
            <?php
        }

        echo $layout->get_html();

        $content = ob_get_clean();
    } catch ( \Exception $e ) {
        $content = __( 'Edition not found.', 'special-editions' );
    }

    return $content;

}

add_filter( 'the_content', __NAMESPACE__ . '\content', 10 );