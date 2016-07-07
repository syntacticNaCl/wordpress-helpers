# WordPress Helpers

A collection of custom WordPress extensions by Zawntech.

**Currently in initial development. API subject to change until a release is tagged.**

### Installation

This plugin must be installed to the must-use plugins directory, ```wp-content/mu-plugins```, by default, so that 
the WordPress extensions provided by this package are loaded *before* regular plugins.

Currently, composer assets must be installed manually.

## PHP Classes

Classes are name spaced to ```Zawntech\WordPress```.

### View

The ```View``` class is an implementation of [Laravel's](https://laravel.com/) [Blade](https://laravel.com/docs/5.2/blade)
templating engine.

In your plugin or theme, you should create a directory ```views```, for example, then register an absolute
path to the directory to the View class via **View::addViewDirectory($absolutePath)**.

```php
<?php
use Zawntech\WordPress\View;

// Register a views directory
View::addViewDirectory( '/path/to/views' );
```

With a view directory registered, we can now render Blade views in our WordPress instance.
```php
$viewName = 'some.view';
$viewData = ['some' => 'data'];
$renderedView = View::render( $viewName, $viewData );
```

