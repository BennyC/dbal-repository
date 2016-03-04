<?php

namespace Fudge\DBAL;

use JsonSerializable;

/**
 * Representation of a single object within a result set
 * This is used as the default class when returning values from
 * the Repository
 */
class Entity implements JsonSerializable
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var array
     */
    protected $protected = [];

    /**
     * @var string
     */
    protected $identifier = "id";

    /**
     * @param array
     */
    public function __construct(array $attributes = [])
    {
        $this->forceFill($attributes);
    }

    /**
     * Populate the attributes array
     * @param array
     * @throws \OutOfBoundsException
     */
    public function fill(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }

    /**
     * Force populated attributes to ensure protected attributes get filled
     * @param array
     */
    public function forceFill(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Return a singular attribute
     * @param string
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (! isset($this->attributes[$key])) {
            return null;
        }

        return $this->attributes[$key];
    }

    /**
     * Set an attribute
     * @param string
     * @param mixed
     * @throws \OutOfBoundsException if the attribute is protected
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->protected)) {
            throw new \OutOfBoundsException(sprintf("%s is a protected attribute", $key));
        }

        $this->attributes[$key] = $value;
    }

    /**
     * Get all attributes
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Return the identifying value
     * @return string
     */
    public function getIdentifier()
    {
        return $this->getAttribute($this->identifier);
    }

    /**
     * Set which attributes are protected
     * @param array
     */
    public function setProtectedAttributes(array $protected)
    {
        $this->protected = $protected;
    }

    /**
     * @see \JsonSerializable::jsonSerialize
     */
    public function jsonSerialize()
    {
        return $this->getAttributes();
    }
}
