<?php
/**
 * Shortcodes
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions;

use SpecialEditions\Editions\Edition;

/**
 * @param array  $atts
 * @param string $content
 *
 * @return string
 */
function directoryShortcode( $atts, $content = '' ) {

    $atts = shortcode_atts( array(), $atts, 'special-editions' );

    $selected_title        = ! empty( $_GET['title'] ) ? wp_strip_all_tags( stripslashes( $_GET['title'] ) ) : '';
    $selected_author       = ! empty( $_GET['author'] ) ? wp_strip_all_tags( stripslashes( $_GET['author'] ) ) : '';
    $selected_age          = ! empty( $_GET['age'] ) ? absint( $_GET['age'] ) : '';
    $selected_genre        = ! empty( $_GET['genre'] ) ? absint( $_GET['genre'] ) : '';
    $selected_attribute    = ! empty( $_GET['attribute'] ) ? absint( $_GET['attribute'] ) : '';
    $selected_availability = isset( $_GET['available'] ) ? filter_var( $_GET['available'], FILTER_VALIDATE_BOOLEAN ) : true;

    $age_groups = \Book_Database\get_book_terms( array(
        'taxonomy' => 'age',
        'number'   => 100,
        'orderby'  => 'name',
        'order'    => 'ASC'
    ) );

    $genres = \Book_Database\get_book_terms( array(
        'taxonomy' => 'genre',
        'number'   => 100,
        'orderby'  => 'name',
        'order'    => 'ASC'
    ) );

    $attributes = Attributes\Database::query( array(
        'number'  => 100,
        'orderby' => 'name',
        'order'   => 'ASC'
    ) );

    $query = new Editions\Query( array(
        'title'     => $selected_title,
        'author'    => $selected_author,
        'age'       => $selected_age,
        'genre'     => $selected_genre,
        'attribute' => $selected_attribute,
        'available' => $selected_availability,
        'per_page' => 20,
        'page'      => $_GET['sepage'] ?? 1
    ) );

    ob_start();
    ?>
    <div id="special-edition-search">
        <form class="jumbotron" method="GET" action="<?php echo esc_url( get_permalink() ); ?>">
            <div class="container">
                <div class="form-row">
                    <div class="col">
                        <div class="form-group">
                            <label for="book-title"><?php _e( 'Book title', 'special-editions' ); ?></label>
                            <input type="text" id="book-title" class="form-control" name="title" placeholder="<?php esc_attr_e( 'Search for a title', 'special-editions' ); ?>" value="<?php echo esc_attr( $selected_title ); ?>">
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-group">
                            <label for="book-author"><?php _e( 'Author', 'special-editions' ); ?></label>
                            <input type="text" id="book-author" class="form-control" name="author" placeholder="<?php esc_attr_e( 'Search for an author', 'special-editions' ); ?>" value="<?php echo esc_attr( $selected_author ); ?>">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-6 col-md-4">
                        <div class="form-group">
                            <label for="book-age-group"><?php _e( 'Filter by age group', 'special-editions' ); ?></label>
                            <select id="book-age-group" class="form-control" name="age">
                                <option value="" <?php selected( '', $selected_age ); ?>><?php _e( 'Any', 'special-editions' ); ?></option>
                                <?php foreach ( $age_groups as $age_group ) : ?>
                                    <option value="<?php echo esc_attr( $age_group->get_id() ); ?>" <?php selected( $age_group->get_id(), $selected_age ); ?>><?php echo esc_html( $age_group->get_name() ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="form-group">
                            <label for="book-genre"><?php _e( 'Filter by genre', 'special-editions' ); ?></label>
                            <select id="book-genre" class="form-control" name="genre">
                                <option value="" <?php selected( '', $selected_genre ); ?>><?php _e( 'Any', 'special-editions' ); ?></option>
                                <?php foreach ( $genres as $genre ) : ?>
                                    <option value="<?php echo esc_attr( $genre->get_id() ); ?>" <?php selected( $genre->get_id(), $selected_genre ); ?>><?php echo esc_html( $genre->get_name() ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="form-group">
                            <label for="book-attribute"><?php _e( 'Filter by attribute', 'special-editions' ); ?></label>
                            <select id="book-attribute" class="form-control" name="attribute">
                                <option value="" <?php selected( '', $selected_attribute ); ?>><?php _e( 'Any', 'special-editions' ); ?></option>
                                <?php foreach ( $attributes as $attr ) : ?>
                                    <option value="<?php echo esc_attr( $attr->get_id() ); ?>" <?php selected( $attr->get_id(), $selected_attribute ); ?>><?php echo esc_html( $attr->getName() ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <div class="form-group">
                            <label for="book-availability">
                                <input type="hidden" name="available" value="0">
                                <input type="checkbox" id="book-availability" name="available" value="1" <?php checked( $selected_availability ); ?>>
                                <?php _e( 'Show available editions only', 'special-editions' ); ?>
                            </label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group text-right">
                            <a href="<?php echo esc_url( get_permalink() ); ?>" id="special-editions-reset-filters"><?php _e( '&times; Reset filters', 'special-editions' ); ?></a>
                            <button type="submit" class="btn btn-primary"><?php _e( 'Search', 'special-editions' ); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div id="special-edition-results" class="container">
            <div id="special-edition-number-results">
                <?php
                printf(
                    _n( '%d book found', '%d books found', $query->getTotalCount(), 'special-editions' ),
                    $query->getTotalCount()
                )
                ?>
            </div>

            <?php if ( $query->hasEditions() ) : ?>
                <div class="row row-cols-4">
                    <?php foreach ( $query->getEditions() as $edition_details ) :
                        $edition = new Edition( $edition_details );
                        $attributes = $edition->getAttributes( array( 'fields' => 'names' ) );
                        $details_url = home_url( '/special-edition/' . urlencode( $edition->get_id() ) . '/' );
                        ?>
                        <div class="col-6 col-md-3">
                            <div class="card h-100">
                                <?php
                                if ( $edition->hasImage() ) {
                                    echo '<a href="' . esc_url( $details_url ) . '">';
                                    $edition->displayImage( 'large' );
                                    echo '</a>';
                                } else {
                                    // @todo
                                }
                                ?>
                                <div class="card-body">
                                    <a href="<?php echo esc_url( $details_url ); ?>">
                                        <?php echo esc_html( sprintf( '%s by %s', $edition_details->title, $edition_details->author_name ) ); ?>
                                    </a>
                                </div>

                                <div class="card-footer">
                                    <?php
                                    if ( ! empty( $attributes ) ) {
                                        foreach ( $attributes as $attribute ) {
                                            ?>
                                            <span class="badge badge-pill badge-secondary"><?php echo esc_html( $attribute ); ?></span>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ( $query->getTotalCount() > count( $query->getEditions() ) ) : ?>
                    <nav id="special-edition-pagination" class="pagination">
                        <?php
                        echo paginate_links( array(
                            'base'      => add_query_arg( 'sepage', '%#%' ),
                            'format'    => '',
                            'prev_text' => __( '&laquo;' ),
                            'next_text' => __( '&raquo;' ),
                            'total'     => ceil( $query->getTotalCount() / $query->getPerPage() ),
                            'current'   => $_GET['sepage'] ?? 1
                        ) )
                        ?>
                    </nav>
                <?php endif; ?>
            <?php else : ?>
                <p><?php _e( 'No special editions found.', 'special-editions' ); ?></p>
            <?php endif; ?>
        </div>

    </div>
    <?php
    return ob_get_clean();

}

add_shortcode( 'special-editions', __NAMESPACE__ . '\directoryShortcode' );
