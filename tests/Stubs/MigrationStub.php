<?php

namespace Stidges\LaravelFkMigration\Tests\Stubs;

use Stidges\LaravelFkMigration\Migration;

class MigrationStub extends Migration
{
    protected $keys = [
        'table_one' => [ 'column' => 'foo', 'on' => 'bar' ],
        'table_two' => [
            [ 'column' => 'bar', 'on' => 'baz' ],
            [ 'column' => ['baz'], 'on' => 'foo' ]
        ]
    ];
}
