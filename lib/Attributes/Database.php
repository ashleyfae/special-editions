<?php
/**
 * Database.php
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Attributes;

/**
 * Class Database
 *
 * @package SpecialEditions\Attributes
 */
class Database extends \SpecialEditions\Database {

    /**
     * Retrieves the query interface
     *
     * @param array $args
     *
     * @return Database\Query
     */
    public static function getQuery( $args = array() ) {
        return new Database\Query( $args );
    }

    /**
     * @param int $id
     *
     * @return Attribute
     * @throws \Exception
     */
    public static function retrieve( $id ) {
        return parent::retrieve( $id );
    }

    /**
     * @param string $column_name
     * @param mixed  $value
     *
     * @return Attribute
     * @throws \Exception
     */
    public static function retrieveBy( $column_name, $value ) {
        return parent::retrieveBy( $column_name, $value );
    }

    /**
     * @param array $args
     *
     * @return Attribute[]
     */
    public static function query( $args = array() ) {
        return parent::query( $args );
    }

    /**
     * @param array $args
     *
     * @return Attribute
     * @throws \Exception
     */
    public static function create( $args ) {
        $args = wp_parse_args( $args, array(
            'name'        => '',
            'slug'        => '',
            'description' => ''
        ) );

        if ( empty( $args['name'] ) ) {
            throw new \InvalidArgumentException( __( 'Attribute name is required.', 'special-editions' ) );
        }

        if ( empty( $args['slug'] ) ) {
            $args['slug'] = sanitize_title( $args['name'] );
        }

        // Generate a slug.
        $args['slug'] = self::generateSlug( $args['slug'] );

        return parent::create( $args );
    }

    /**
     * @param int   $item_id
     * @param array $args
     *
     * @throws \Exception
     */
    public static function update( $item_id, $args ) {
        $attribute = self::retrieve( $item_id );

        if ( ! $attribute instanceof Attribute ) {
            throw new \Exception( __( 'Attribute not found.', 'special-editions' ) );
        }

        // If the slug is changing, let's regenerate it.
        if ( isset( $args['slug'] ) && $args['slug'] != $attribute->getSlug() ) {
            $args['slug'] = self::generateSlug( $args['slug'] );
        }

        parent::update( $item_id, $args );
    }

    /**
     * Generates a unique slug
     *
     * @param string $desired_slug First choice of slug
     *
     * @return string
     */
    public static function generateSlug( $desired_slug ) {

        $desired_slug = sanitize_title( $desired_slug );

        // Check if this slug already exists.
        $attributes = self::count( array(
            'slug' => $desired_slug
        ) );

        $new_slug = $desired_slug;

        if ( $attributes ) {
            $suffix = 2;

            do {
                $alt_slug   = _truncate_post_slug( $desired_slug, 200 - ( strlen( $suffix ) + 1 ) ) . '-' . $suffix;
                $attributes = self::count( array(
                    'slug' => $alt_slug
                ) );

                $suffix++;
            } while ( $attributes );

            $new_slug = $alt_slug;
        }

        return $new_slug;

    }

    /**
     * Retrieves the attributes attached to a given edition
     *
     * @param int   $edition_id ID of the edition
     * @param array $args       Arguments to override the default.
     *
     * @return Attribute[]|array
     */
    public static function retrieveAttachedAttributes( $edition_id, $args = array() ) {

        global $wpdb;

        $args = wp_parse_args( $args, array(
            'orderby' => 'name',
            'order'   => 'ASC',
            'fields'  => ''
        ) );

        // Select this.
        $select_this = 'attr.*';
        if ( in_array( $args['fields'], array( 'id', 'ids' ) ) ) {
            $select_this = 'attr.id';
        } elseif ( in_array( $args['fields'], array( 'name', 'names' ) ) ) {
            $select_this = 'attr.name';
        }

        // Orderby
        $orderby = $args['orderby'];
        $order   = $args['order'];

        if ( in_array( $orderby, array( 'id', 'name', 'slug', 'date_created', 'date_modified' ) ) ) {
            $orderby = "attr.$orderby";
        } elseif ( 'none' === $orderby ) {
            $orderby = '';
            $order   = '';
        } else {
            $orderby = 'attr.id';
        }

        if ( ! empty( $orderby ) ) {
            $orderby = "ORDER BY $orderby";
        }

        $order = strtoupper( $order );
        if ( '' !== $order && ! in_array( $order, array( 'ASC', 'DESC' ) ) ) {
            $order = 'ASC';
        }

        $query = $wpdb->prepare(
            "SELECT {$select_this} FROM {$wpdb->bdb_special_edition_attributes} AS attr
            INNER JOIN {$wpdb->bdb_special_edition_attribute_relationships} AS r ON( attr.id = r.attribute_id )
            WHERE edition_id = %d
            {$orderby} {$order}",
            absint( $edition_id )
        );

        $attributes = array();

        if ( empty( $args['fields'] ) ) {
            $results = $wpdb->get_results( $query );

            if ( ! empty( $results ) && is_array( $results ) ) {
                foreach ( $results as $attribute ) {
                    $attributes[] = new Attribute( $attribute );
                }
            }
        } elseif ( in_array( $args['fields'], array( 'id', 'ids', 'name', 'names' ) ) ) {
            $attributes = $wpdb->get_col( $query );
        }

        return $attributes;

    }

    /**
     * Associates attributes with a given edition
     *
     * @param int              $edition_id ID of the edition to add attributes to.
     * @param array|int|string $attributes Single attribute name/ID or array of attribute names/IDs.
     * @param bool             $append     False to delete the difference of terms, true to append to the existing.
     *
     * @return void
     * @throws \Exception
     */
    public static function setAttributes( int $edition_id, $attributes, $append = false ) {

        global $wpdb;

        if ( ! is_array( $attributes ) ) {
            $attributes = array( $attributes );
        }

        // Get existing attributes.
        if ( ! $append ) {
            $old_attribute_ids = self::retrieveAttachedAttributes( $edition_id, array( 'fields' => 'id' ) );
        } else {
            $old_attribute_ids = array();
        }

        $all_attribute_ids = array();

        foreach ( $attributes as $attribute ) {

            // $attribute is either an attribute name or ID.
            if ( ! strlen( trim( $attribute ) ) ) {
                continue;
            }

            if ( is_int( $attribute ) ) {

                // We have an attribute ID.

                $attribute_id = absint( $attribute );

                // If this attribute doesn't exist - skip it.
                try {
                    $attribute_object = self::retrieve( $attribute_id );
                } catch ( \Exception $e ) {
                    continue;
                }

                $all_attribute_ids[] = $attribute_id;

            } else {

                // We have an attribute name.

                // Check to see if it already exists.
                try {
                    $attribute_object = self::retrieveBy( 'name', $attribute );
                } catch ( \Exception $e ) {
                    echo $e->getMessage();
                    try {
                        $attribute_object = self::create( array(
                            'name' => $attribute
                        ) );
                    } catch ( \Exception $e ) {
                        continue;
                    }
                }

                if ( ! $attribute_object instanceof Attribute ) {
                    continue;
                }

                $all_attribute_ids[] = $attribute_object->get_id();

            }

            // If the attribute relationship already exists, let's move on.
            try {
                RelationshipsDatabase::retrieveByAttributeAndEdition( $attribute_object->get_id(), $edition_id );
            } catch ( \Exception $e ) {
                // If there is no relationship, create a new one.
                try {
                    RelationshipsDatabase::create( array(
                        'attribute_id' => $attribute_object->get_id(),
                        'edition_id'   => $edition_id
                    ) );
                } catch ( \Exception $e ) {

                }
            }

        }

        if ( ! $append ) {

            // Delete the differing relationships.
            $delete_attribute_ids = array_diff( $old_attribute_ids, $all_attribute_ids );

            if ( $delete_attribute_ids ) {
                $attribute_id_string = implode( ',', array_map( 'absint', $delete_attribute_ids ) );

                $wpdb->query( $wpdb->prepare(
                    "DELETE FROM {$wpdb->bdb_special_edition_attribute_relationships}
                    WHERE edition_id = %d
                    AND attribute_id IN({$attribute_id_string})",
                    absint( $edition_id )
                ) );
            }

        }

    }

}