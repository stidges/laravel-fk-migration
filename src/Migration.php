<?php

namespace Stidges\LaravelFkMigration;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration as BaseMigration;

abstract class Migration extends BaseMigration
{
    /**
     * The foreign keys to create or drop.
     *
     * @var array
     */
    protected $keys = [];

    /**
     * The schema builder instance.
     *
     * @var \Illuminate\Database\Schema\Builder
     */
    protected $schema;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->getPreparedKeys() as $collection) {
            $this->createKeys($collection);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->getPreparedKeys() as $collection) {
            $this->dropKeys($collection);
        }
    }

    /**
     * Prepare the defined keys.
     *
     * @return array
     */
    public function getPreparedKeys()
    {
        list($tables, $keys) = array_divide($this->getKeys());

        return array_map([$this, 'buildCollection'], $tables, $keys);
    }

    /**
     * Get the raw defined keys.
     *
     * @return array
     */
    public function getKeys()
    {
        return $this->keys;
    }

    /**
     * Get the schema builder instance.
     *
     * @return \Illuminate\Database\Schema\Builder
     */
    public function getSchemaBuilder()
    {
        if (is_null($this->schema)) {
            $this->schema = DB::getSchemaBuilder();
        }

        return $this->schema;
    }

    /**
     * Set the schema builder instance.
     *
     * @param  \Illuminate\Database\Schema\Builder  $schema
     * @return void
     */
    public function setSchemaBuilder(Builder $schema)
    {
        $this->schema = $schema;
    }

    /**
     * Build a foreign key collection from the given table name and keys.
     *
     * @param  string  $tableName
     * @param  array   $keys
     * @return \Stidges\LaravelFkMigration\ForeignKeyCollection
     */
    protected function buildCollection($tableName, array $keys)
    {
        $collection = new ForeignKeyCollection($tableName);

        foreach ($this->formatKeys($keys) as $key) {
            $collection->add(new ForeignKey($key));
        }

        return $collection;
    }

    /**
     * Format the given keys to be a multidimensional array.
     *
     * @param  array  $keys
     * @return array
     */
    protected function formatKeys(array $keys)
    {
        return is_array(current($keys)) ? $keys : [$keys];
    }

    /**
     * Create the foreign keys in the database from the given collection.
     *
     * @param  \Stidges\LaravelFkMigration\ForeignKeyCollection  $collection
     * @return void
     */
    protected function createKeys(ForeignKeyCollection $collection)
    {
        $this->createOrDropKeys($collection, false);
    }

    /**
     * Drop the foreign keys in the database from the given collection.
     *
     * @param  \Stidges\LaravelFkMigration\ForeignKeyCollection  $collection
     * @return void
     */
    protected function dropKeys(ForeignKeyCollection $collection)
    {
        $this->createOrDropKeys($collection, true);
    }

    /**
     * Create or drop the foreign keys in the database.
     *
     * @param  \Stidges\LaravelFkMigration\ForeignKeyCollection  $collection
     * @param  bool  $drop
     * @return void
     */
    protected function createOrDropKeys(ForeignKeyCollection $collection, $drop)
    {
        $method = $drop ? 'dropKey' : 'createKey';
        $tableName = $collection->getTable();

        $this->getSchemaBuilder()->table($tableName, function(Blueprint $table) use ($collection, $method) {
            foreach ($collection as $key) {
                $this->{$method}($table, $key);
            }
        });
    }

    /**
     * Create a foreign key on the given table.
     *
     * @param  \Illuminate\Database\Schema\Blueprint   $table
     * @param  \Stidges\LaravelFkMigration\ForeignKey  $key
     * @return void
     */
    protected function createKey(Blueprint $table, ForeignKey $key)
    {
        $table->foreign($key->column)
              ->references($key->references)->on($key->on)
              ->onUpdate($key->onUpdate)
              ->onDelete($key->onDelete);
    }

    /**
     * Drop a foreign key in the given table.
     *
     * @param  \Illuminate\Database\Schema\Blueprint   $table
     * @param  \Stidges\LaravelFkMigration\ForeignKey  $key
     * @return void
     */
    protected function dropKey($table, ForeignKey $key)
    {
        $column = is_array($key->column) ? $key->column : [$key->column];

        $table->dropForeign($column);
    }
}
