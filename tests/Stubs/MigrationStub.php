<?php

namespace Stidges\LaravelFkMigration\Tests\Stubs;

use Stidges\LaravelFkMigration\Migration;

class MigrationStub extends Migration
{
    protected $keys = [
        'table_one' => [ 'column' => 'bar_id' ],
        'table_two' => [
            [ 'column' => 'bar_id' ],
            [ 'column' => ['foo_id', 'bar_id'] ]
        ]
    ];
}
