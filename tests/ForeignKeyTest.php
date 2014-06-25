<?php

namespace Stidges\LaravelFkMigration\Tests;

use Stidges\LaravelFkMigration\ForeignKey;

class ForeignKeyTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_assigns_options_to_properties()
    {
        $options = [
            'column'     => 'bar_id',
            'references' => 'id',
            'on'         => 'bars',
            'onUpdate'   => 'cascade',
            'onDelete'   => 'restrict',
        ];

        $key = new ForeignKey($options);

        $this->assertEquals('bar_id', $key->column);
        $this->assertEquals('id', $key->references);
        $this->assertEquals('bars', $key->on);
        $this->assertEquals('cascade', $key->onUpdate);
        $this->assertEquals('restrict', $key->onDelete);
        $this->assertFalse(property_exists($key, 'nonExistingProperty'));
    }

    /** @test */
    public function it_does_not_assign_non_existing_properties()
    {
        $options = [
            'column' => 'bar_id',
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
        $key = new ForeignKey([]);
    }

    /** @test */
    public function it_makes_a_table_name_from_column_if_on_option_is_not_passed()
    {
        $options = [ 'column' => 'bar_id' ];
        $key = new ForeignKey($options);

        $this->assertEquals('bars', $key->on);
    }

    /** @test */
    public function it_makes_a_table_name_from_first_column_if_column_is_an_array()
    {
        $options = [ 'column' => [ 'bar_id', 'baz_id' ] ];
        $key = new ForeignKey($options);

        $this->assertEquals('bars', $key->on);
    }
}
