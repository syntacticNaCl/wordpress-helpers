# WordPress Helpers

A collection of custom WordPress extensions and HTML widgets by Zawntech.

**Currently in initial development. API subject to change until a release is tagged.**

### Installation

This plugin must be installed to the must-use plugins directory, ```wp-content/mu-plugins```, by default, so that 
the WordPress extensions provided by this package are loaded *before* regular plugins.

Currently, composer assets must be installed manually.

## Features

+ Custom, non-colliding implementation of Twitter Bootstrap hooked into WordPress administration screens.
+ Extensible PHP classes wrapping various WordPress functions and methods for easily implementing or hooking:
  + custom post types
  + metaboxes
  + quick editor
  + eloquent post meta
+ Adds the ability to relate posts types in a 1-1, non-hierarchical via a new posts_pivot database table.

## PHP Classes

Classes are name spaced to ```Zawntech\WordPress```.

### View

The ```View``` class is an implementation of [Laravel's](https://laravel.com/) [Blade](https://laravel.com/docs/5.2/blade)
templating engine.

In your plugin or theme, you should create a directory ```views```, for example, then register an absolute
path to the directory to the View class via **View::addViewDirectory($absolutePath)**.

```php
<?php
use Zawntech\WordPress\Utility\View;

// Register a views directory
View::addViewDirectory( '/path/to/views' );
```

With a view directory registered, we can now render Blade views in our WordPress instance.
```php
$viewName = 'some.view';
$viewData = ['some' => 'data'];
$renderedView = View::render( $viewName, $viewData );
```

