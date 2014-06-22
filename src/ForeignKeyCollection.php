<?php

namespace Stidges\LaravelFkMigration;

use Countable;
use ArrayIterator;
use IteratorAggregate;

class ForeignKeyCollection implements IteratorAggregate, Countable
{
    /**
     * The foreign keys to be created or dropped for the table.
     *
     * @var \Stidges\LaravelFkMigration\ForeignKey[]
     */
    protected $foreignKeys;

    /**
     * The table to create or drop the foreign keys for.
     *
     * @var string
     */
    protected $table;

    /**
     * Create a new foreign key collection instance.
     *
     * @param  string  $table
     * @return self
     */
    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * Get the table for which the foreign keys will be created or dropped.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Add a foreign key to the collection.
     *
     * @param  \Stidges\LaravelFkMigration\ForeignKey  $foreignKey
     * @return self
     */
    public function add(ForeignKey $foreignKey)
    {
        $this->foreignKeys[] = $foreignKey;

        return $this;
    }

    /**
     * Get an iterator for the foreign keys in the collection.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->foreignKeys);
    }

    /**
     * Count the number of foreign keys in the collection.
     *
     * @return int
     */
    public function count()
    {
        return count($this->foreignKeys);
    }
}
