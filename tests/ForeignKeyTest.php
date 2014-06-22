<?php

namespace Stidges\LaravelFkMigration\Tests;

use Stidges\LaravelFkMigration\ForeignKey;

class ForeignKeyTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_assigns_options_to_properties()
    {
        $options = [
            'column' => 'foo',
            'references' => 'id',
            'on' => 'bar',
            'onUpdate' => 'cascade',
            'onDelete' => 'restrict',
        ];

        $key = new ForeignKey($options);

        $this->assertEquals('foo', $key->column);
        $this->assertEquals('id', $key->references);
        $this->assertEquals('bar', $key->on);
        $this->assertEquals('cascade', $key->onUpdate);
        $this->assertEquals('restrict', $key->onDelete);
        $this->assertFalse(property_exists($key, 'nonExistingProperty'));
    }

    /** @test */
    public function it_does_not_assign_non_existing_properties()
    {
        $options = [
            'column' => 'foo',
            'on' => 'bar',
            'nonExistingProperty' => 'foo',
        ];

        $key = new ForeignKey($options);

        $this->assertFalse(property_exists($key, 'nonExistingProperty'));
    }

    /**
     * @test
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage column
     */
    public function it_throws_an_exception_if_column_option_is_missing()
    {
        $options = [ 'on' => 'bar' ];

        $key = new ForeignKey($options);
    }

    /**
     * @test
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage on
     */
    public function it_throws_an_exception_if_on_option_is_missing()
    {
        $options = [ 'column' => 'foo' ];

        $key = new ForeignKey($options);
    }
}
