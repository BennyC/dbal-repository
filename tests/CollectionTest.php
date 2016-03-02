<?php

namespace Fudge\DBAL;

/**
 * @coversDefaultClass \Fudge\DBAL\Collection
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @coversNothing
     */
    public function testExists()
    {
        $this->assertTrue(class_exists("Fudge\DBAL\Collection"));
    }

    /**
     * @covers ::__construct
     * @covers ::getItems
     */
    public function testSettingOfItemsViaConstruction()
    {
        $items = [1, 2, 3];
        $collection = new Collection($items);

        $this->assertEquals($items, $collection->getItems());
    }

    /**
     * @covers ::getIterator
     */
    public function testActsLikeArray()
    {
        $items = [1, 2, 3];
        $collection = new Collection($items);

        foreach ($collection as $key => $value) {
            $this->assertEquals($items[$key], $value);
        }
    }

    /**
     * @covers ::copy
     * @covers ::__clone
     */
    public function testCopyingOfCollection()
    {
        $items = [1, 2, 3];
        $collection = new Collection($items);
        $copy = $collection->copy();
        $clone = clone $collection;

        $this->assertTrue($collection !== $copy);
        $this->assertTrue($collection !== $clone);
        $this->assertEquals($collection->getItems(), $copy->getItems());
        $this->assertEquals($collection->getItems(), $clone->getItems());
    }

    /**
     * @covers ::filter
     */
    public function testFilteringOfCollection()
    {
        $items = range(1, 10);
        $collection = new Collection($items);
        $newCollection = $collection->filter(function ($a) {
            return $a > 5;
        });

        $this->assertCount(5, $newCollection);
        $this->assertCount(10, $collection);
    }

    /**
     * @covers ::sort
     */
    public function testSortingOfCollection()
    {
        $items = range(1, 10);
        $collection = new Collection($items);
        $newCollection = $collection->sort(function ($a, $b) {
            return $b - $a;
        });

        $this->assertEquals(array_reverse($items), $newCollection->getItems());
        $this->assertTrue($newCollection[0] === 10);
    }

    /**
     * @covers ::count
     */
    public function testCountingOfItems()
    {
        $items = [1, 2, 3, 4];
        $collection = new Collection($items);
        $this->assertEquals(4, $collection->count());
    }

    /**
     * @covers ::offsetExists
     */
    public function testOffsetExists()
        {
        $items = [1, 2, 3, 4];
        $collection = new Collection($items);

        $this->assertTrue(isset($collection[0]));
        $this->assertFalse(isset($collection["does not exist"]));
    }

    /**
     * @covers ::offsetSet
     * @covers ::offsetGet
     */
    public function testOffsetSetAndGet()
    {
        $collection = new Collection([]);
        $collection[0] = 1;

        $this->assertEquals(1, $collection[0]);
    }

    /**
     * @covers ::offsetUnset
     */
    public function testOffsetUnset()
    {
        $collection = new Collection([1]);
        unset($collection[0]);

        $this->assertFalse(isset($collection[0]));
    }
}
