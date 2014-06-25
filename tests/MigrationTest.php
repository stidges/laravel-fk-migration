<?php

namespace Stidges\LaravelFkMigration\Tests;

use Mockery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Facade;
use Stidges\LaravelFkMigration\ForeignKey;
use Stidges\LaravelFkMigration\ForeignKeyCollection;
use Stidges\LaravelFkMigration\Tests\Stubs\MigrationStub;

class MigrationTest extends \PHPUnit_Framework_TestCase
{
    private $migration;

    public function setUp()
    {
        $this->schema = Mockery::mock('Illuminate\Database\Schema\Builder');
        $this->migration = new MigrationStub;
        $this->migration->setSchemaBuilder($this->schema);
    }

    /** @test */
    public function it_creates_foreign_keys_on_up()
    {
        $this->schema->shouldReceive('table')->once()->with('table_one', Mockery::on(function($closure) {
            $blueprint = Mockery::mock('Illuminate\Database\Schema\Blueprint');
            $fluent = Mockery::mock();

            $blueprint->shouldReceive('foreign')->once()->with('bar_id')->andReturn($fluent);
            $fluent->shouldReceive('references')->once()->andReturn(Mockery::self());
            $fluent->shouldReceive('on')->once()->with('bars')->andReturn(Mockery::self());
            $fluent->shouldReceive('onUpdate->onDelete')->once();

            $closure($blueprint);
            return true;
        }));

        $this->schema->shouldReceive('table')->once()->with('table_two', Mockery::on(function($closure) {
            $blueprint = Mockery::mock('Illuminate\Database\Schema\Blueprint');
            $fluent = Mockery::mock();

            $blueprint->shouldReceive('foreign')->once()->with('bar_id')->andReturn($fluent);
            $blueprint->shouldReceive('foreign')->once()->with(['foo_id', 'bar_id'])->andReturn($fluent);
            $fluent->shouldReceive('references')->twice()->andReturn(Mockery::self());
            $fluent->shouldReceive('on')->once()->with('bars')->andReturn(Mockery::self());
            $fluent->shouldReceive('on')->once()->with('foos')->andReturn(Mockery::self());
            $fluent->shouldReceive('onUpdate->onDelete')->twice();

            $closure($blueprint);
            return true;
        }));

        $this->migration->up();
    }

    /** @test */
    public function it_drops_foreign_keys_on_down()
    {
        $this->schema->shouldReceive('table')->once()->with('table_one', Mockery::on(function($closure) {
            $blueprint = Mockery::mock('Illuminate\Database\Schema\Blueprint');

            $blueprint->shouldReceive('dropForeign')->once()->with(['bar_id']);

            $closure($blueprint);
            return true;
        }));

        $this->schema->shouldReceive('table')->once()->with('table_two', Mockery::on(function($closure) {
            $blueprint = Mockery::mock('Illuminate\Database\Schema\Blueprint');

            $blueprint->shouldReceive('dropForeign')->once()->with(['bar_id']);
            $blueprint->shouldReceive('dropForeign')->once()->with(['foo_id', 'bar_id']);

            $closure($blueprint);
            return true;
        }));

        $this->migration->down();
    }

    /** @test */
    public function it_formats_the_keys_array_into_collections_of_foreign_keys()
    {
        $collections = $this->migration->getPreparedKeys();

        $this->assertCount(2, $collections);
        $this->assertTrue($collections[0] instanceof ForeignKeyCollection);
        $this->assertTrue($collections[1] instanceof ForeignKeyCollection);
        $this->assertEquals('table_one', $collections[0]->getTable());
        $this->assertEquals('table_two', $collections[1]->getTable());
        $this->assertCount(1, $collections[0]);
        $this->assertCount(2, $collections[1]);
    }

    /** @test */
    public function it_returns_the_schema_builder()
    {
        $migration = new MigrationStub;
        $mock = Mockery::mock('Illuminate\Database\Connection');

        Facade::setFacadeApplication([ 'db' => $mock ]);

        $mock->shouldReceive('getSchemaBuilder')->once()->andReturn('foo');

        $this->assertEquals('foo', $migration->getSchemaBuilder());
    }
}
