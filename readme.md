# WordPress Helpers

A collection of custom WordPress extensions and HTML widgets by Zawntech.

**Currently in initial development. API subject to change until a release is tagged.**

### Installation

This plugin must be installed to the must-use plugins directory, ```wp-content/mu-plugins```, by default, so that 
the WordPress extensions provided by this package are loaded *before* regular plugins.

Currently, composer packages must be installed manually via the ```composer install``` command from the installation
directory. An exception will be thrown if composer packages are not found at runtime.

## Goal

The goal of this package is to provide a set of extensible web assets that expedite WordPress development, reducing
code overhead for common data modelling tasks in WordPress. 

## Features

+ A highly customizable SASS Bootstrap configuration integration hooked into WordPress administration screens.
+ Adds the ability to relate posts types in a 1:1, non-hierarchical via a new posts_pivot database table; think post 
  types as taxonomies.
+ A modularized view system utilizing [Laravel's](https://laravel.com/) with a hierarchical template inheritance model
  that can be hooked or overridden from plugins and themes.
+ Extensible PHP classes wrapping various WordPress functions and methods for easily implementing or hooking:
  + custom post types
  + post meta
  + metaboxes
+ A custom input/output module (in progress)

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

