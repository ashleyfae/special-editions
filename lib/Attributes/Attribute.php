<?php
/**
 * Attribute
 *
 * @package   special-editions
 * @copyright Copyright (c) 2020, Ashley Gibson
 * @license   GPL2+
 */

namespace SpecialEditions\Attributes;

/**
 * Class Attribute
 *
 * @package SpecialEditions\Attributes
 */
class Attribute extends \Book_Database\Base_Object {

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $slug = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * Retrieves the name of the attribute
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Retrieves the attribute slug
     *
     * @return string
     */
    public function getSlug() {
        return $this->slug;
    }

    /**
     * Retrieves the attribute description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

}