<?php

namespace Fudge\DBAL;

/**
 * @coversDefaultClass \Fudge\DBAL\Entity
 */
class EntityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::fill
     * @covers ::getAttribute
     */
    public function testCreationOfAnEntity()
    {
        $entity = new Entity();
        $entity->fill(["hello" => "world"]);

        $this->assertEquals("world", $entity->getAttribute("hello"));
    }

    /**
     * @covers ::__construct
     * @covers ::forceFill
     * @covers ::getAttributes
     */
    public function testGettingOfAllAttributes()
    {
        $attributes = ["a" => 1, "b" => 2, "c" => 3];
        $entity = new Entity($attributes);

        $this->assertCount(count($attributes), $entity->getAttributes());
    }

    /**
     * @covers ::getAttribute
     */
    public function testGettingAnUnsetAttribute()
    {
        $entity = new Entity();
        $this->assertNull($entity->getAttribute("hello"));
    }

    /**
     * @covers ::getIdentifier
     */
    public function testGettingOfIdentifier()
    {
        $entity = new Entity(["id" => 123]);
        $this->assertEquals(123, $entity->getIdentifier());
    }

    /**
     * @covers ::setAttribute
     */
    public function testSettingOfAttribute()
    {
        $entity = new Entity();
        $entity->setAttribute("name", "foo");

        $this->assertEquals("foo", $entity->getAttribute("name"));
    }

    /**
     * @covers ::setProtectedAttributes
     * @covers ::setAttribute
     */
    public function testSettingOfAProtectedAttribute()
    {
        $entity = new Entity();
        $entity->setProtectedAttributes(["id"]);

        $this->setExpectedException("OutOfBoundsException");
        $entity->setAttribute("id", 123);
    }

    /**
     * @covers ::jsonSerialize
     */
    public function testJsonSerialize()
    {
        $data = ["username" => "Ben"];
        $entity = new Entity($data);
        $jsonArray = $entity->jsonSerialize();


        $this->assertInstanceOf("JsonSerializable", $entity);
        $this->assertEquals($data, $jsonArray);
    }
}
