<?php
/**
 * Add/Edit Special Edition
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Admin;

use SpecialEditions\Editions\Database;
use SpecialEditions\Editions\Edition;
use SpecialEditions\Utils\Pages;

try {
    if ( empty( $_GET['edition_id'] ) ) {
        throw new \Exception();
    }

    $edition = Database::retrieve( absint( $_GET['edition_id'] ) );
} catch ( \Exception $e ) {
    $edition = false;
}

if ( $edition instanceof Edition ) {
    $book_id = $edition->getBookID();
} elseif ( isset( $_GET['book_id'] ) ) {
    $book_id = absint( $_GET['book_id'] );
} else {
    $book_id = false;
}

if ( empty( $book_id ) ) {
    wp_die( __( 'Missing book ID.', 'special-editions' ) );
}
?>
<div class="wrap">
    <h1><?php echo $edition instanceof Edition ? __( 'Edit Special Edition', 'special-editions' ) : __( 'Add Special Edition', 'special-editions' ); ?></h1>

    <form id="bdb-special-editions-edit-edition" class="bdb-edit-object" method="POST" action="">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div id="postbox-container-1" class="postbox-container">
                        <div id="side-sortables" class="meta-box-sortables ui-sortables">
                            <div id="submitdiv" class="postbox">
                                <h2 class="hndle ui-sortable handle"><?php _e( 'Save', 'book-database' ); ?></h2>
                                <div class="inside">
                                    <div id="major-publishing-actions">
                                        <div id="delete-action">
                                            <?php if ( $edition instanceof Edition ) : ?>
                                                <a href="<?php echo esc_url( Pages::getDeleteEditionURL( $edition->get_id() ) ); ?>" class="bdb-delete-item" data-object="<?php esc_attr_e( 'Special Edition', 'special-editions' ); ?>"><?php _e( 'Delete Edition', 'special-editions' ); ?></a>
                                            <?php endif; ?>
                                        </div>
                                        <div id="publishing-action">
                                            <input type="submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Save', 'special-editions' ); ?>">
                                        </div>
                                    </div><!--/#major-publishing-actions-->
                                </div><!--/.inside-->
                            </div><!--/#submitdiv-->
                        </div><!--/#side-sortables-->
                    </div><!--/#postbox-container-1-->

                    <div id="postbox-container-2" class="postbox-container">
                        <div class="postbox">
                            <h2><?php _e( 'Edition Information', 'special-editions' ); ?></h2>
                            <div class="inside">

                                <?php
                                /**
                                 * Used to hook in fields.
                                 *
                                 * @param Edition|false $edition
                                 */
                                do_action( 'special-editions/edit/fields', $edition );
                                ?>

                            </div><!--/.inside-->
                        </div><!--/.postbox-->
                    </div><!--/#postbox-container-2-->
                </div><!--/#post-body-content-->
            </div><!--/#post-body-->
        </div><!--/#poststuff-->

        <input type="hidden" name="book_id" value="<?php echo esc_attr( absint( $book_id ) ); ?>">

        <?php
        if ( $edition instanceof Edition ) {
            wp_nonce_field( 'special_editions_update_edition', 'special_editions_update_edition_nonce' );
            ?>
            <input type="hidden" name="edition_id" value="<?php echo esc_attr( $edition->get_id() ); ?>">
            <?php
        } else {
            wp_nonce_field( 'special_editions_add_edition', 'special_editions_add_edition_nonce' );
        }
        ?>
    </form>
</div>