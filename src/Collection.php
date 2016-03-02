<?php

namespace Fudge\DBAL;

use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use IteratorAggregate;

/**
 * Used to represent a Collection of Models which wil be fetched via the
 * Fudge\DBAL\Repository
 */
class Collection implements ArrayAccess, IteratorAggregate, Countable
{
    /**
     * @var array
     * @access protected
     */
    protected $items;

    /**
     * @param array
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Get items
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Create a copy of the Collection
     * @return static
     */
    public function copy()
    {
        return new static($this->getItems());
    }

    /**
     * Create a copy of the Collection filtered by a provided callback
     * @param Closure
     * @return static
     */
    public function filter(Closure $callback)
    {
        $items = array_filter($this->getItems(), $callback);
        return new static($items);
    }

    /**
     * Create a copy of the Collection sorted by a provided callback, will
     * assign new keys to the Collection
     * @param Closure
     * @return static
     */
    public function sort(Closure $callback)
    {
        $items = $this->getItems();
        usort($items, $callback);

        return new static($items);
    }

    /**
     * @see \ArrayAccess::offsetExists
     */
    public function offsetExists($index)
    {
        return array_key_exists($index, $this->getItems());
    }

    /**
     * @see \ArrayAccess::offsetGet
     */
    public function offsetGet($index)
    {
        return $this->getItems()[$index];
    }

    /**
     * @see \ArrayAccess::offsetSet
     */
    public function offsetSet($index, $value)
    {
        $this->items[$index] = $value;
    }

    /**
     * @see \ArrayAccess::offsetUnset
     */
    public function offsetUnset($index)
    {
        unset($this->items[$index]);
    }

    /**
     * @see \Countable::count
     */
    public function count()
    {
        return count($this->getItems());
    }

    /**
     * @see \IteratorAggregate::getIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getItems());
    }

    /**
     * Return a new copy when cloning
     * @return self
     */
    public function __clone()
    {
        return $this->copy();
    }
}
