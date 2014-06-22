<?php

namespace Stidges\LaravelFkMigration\Tests;

use Stidges\LaravelFkMigration\ForeignKey;
use Stidges\LaravelFkMigration\ForeignKeyCollection;

class ForeignKeyCollectionTest extends \PHPUnit_Framework_TestCase
{
    private $collection;

    public function setUp()
    {
        $this->collection = new ForeignKeyCollection('table');
    }

    /** @test */
    public function it_allows_to_retrieve_the_table_name()
    {
        $this->assertEquals('table', $this->collection->getTable());
    }

    /** @test */
    public function it_is_iteratable()
    {
        $this->assertInstanceOf('IteratorAggregate', $this->collection);
    }

    /** @test */
    public function it_is_countable()
    {
        $this->assertInstanceOf('Countable', $this->collection);
        $this->assertCount(0, $this->collection);
    }

    /** @test */
    public function it_allows_foreign_keys_to_be_added()
    {
        $key = new ForeignKey([ 'column' => 'foo', 'on' => 'bar' ]);

        $this->assertCount(1, $this->collection->add($key));
        $this->assertCount(2, $this->collection->add($key));
    }
}
