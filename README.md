Laravel FK Migration
====================

This [Laravel](http://www.laravel.com) package provides a base migration for you to extend to easily create all your foreign keys. If you ever ran into the problem where you had to reorganize all your migrations due to failing foreign key constraints, this is the package for you!

## Getting Started

This package can be installed through [Composer](http://www.getcomposer.org), just add it to your composer.json file:

```json
{
    "require": {
        "stidges/laravel-fk-migration": "1.0.*"
    }
}
```

After you have added it to your composer.json file, make sure you update your dependencies:

```sh
composer update
```

## Basic Usage

To get started, create a new class in your `app/database/migrations/` directory.
If you want to make sure this migration gets executed last, you can name it something like `9999_99_99_999999_create_foreign_keys.php` (this might be slightly overdone, but you get the idea).

Next, add the following content to the empty class you just created:

```php
<?php

use Stidges\LaravelFkMigration\Migration;

class CreateForeignKeys extends Migration {

    /**
     * The foreign keys to create or drop.
     *
     * @var array
     */
    protected $keys = [];

}
```

The `$keys` array is where you can define your foreign keys. It should be an associative array, where the key is the table name, and the value is a (list of) foreign key(s). Below you can find a list of options that can be specified for the foreign keys.

| Key          | Default      | Description                                                              |
|:-------------|:------------:|:-------------------------------------------------------------------------|
| `column`     | *none*       | The column on which to create the foreign key.                           |
| `references` | `'id'`       | The referenced column in the foreign table.                              |
| `on`         | *none*       | The referenced table.                                                    |
| `onUpdate`   | `'cascade'`  | The referential action to execute when the referenced column is updated. |
| `onDelete`   | `'restrict'` | The referential action to execute when the referenced column is deleted. |

**Note:** As a minimum you should specify the `column` and `on` property for each foreign key. If you forget to specify either of these, an exception will be thrown.

## Basic Example

Below you can find a basic example for reference.

```php
<?php

use Stidges\LaravelFkMigration\Migration;

class CreateForeignKeys extends Migration {

    protected $keys = [
        'posts'    => [ 'column' => 'category_id', 'on' => 'categories' ],
        'post_tag' => [
            [ 'column' => 'post_id', 'on' => 'posts', 'onDelete' => 'cascade' ],
            [ 'column' => 'tag_id',  'on' => 'tags' ],
        ],
    ];
    
}
```

## Extended Example

Internally, the migration will call a `getKeys()` method, which by default returns the specified `$keys` array. You are free to override this method if you wish to have more flexibility when defining keys. For example, if you have a lot of tables referencing the `users` table, you can do the following:

```php
<?php

use Stidges\LaravelFkMigration\Migration;

class CreateForeignKeys extends Migration {

    protected $keys = [];
    
    protected $presets = [
        'user' => [ 'column' => 'user_id', 'on' => 'users' ],
    ];
    
    public function getKeys()
    {
        $keys = [
            'posts'      => [ $this->presets['user'] ],
            'tags'       => [ $this->presets['user'] ],
            'categories' => [ $this->presets['user'] ],
        ];
        
        return $keys;
    }
}
```

This way you don't have to copy the same foreign key reference over and over!

## Contributing

All suggestions and pull requests are welcome! If you make any substantial changes, please provide tests along with your pull requests!

## License

Copyright (c) 2014 Stidges - Released under the [MIT license](LICENSE).
