/**
 * Include elixir.
 * @type {Elixir}
 */
var elixir = require('laravel-elixir');

/**
 * Path to plugin directory.
 * @type {string}
 */
var assetPath = './assets';

elixir(function(mix) {
    mix
    // Copy Knockout and plugins
        .copy( './bower_components/knockout/dist/knockout.debug.js', assetPath + '/js/lib/knockout.debug.js' )
        .copy( './bower_components/knockout-mapping/knockout.mapping.js', assetPath + '/js/lib/knockout.mapping.js' )
        .copy( './bower_components/knockout-components/build/zawntech-knockout-components.js', assetPath + '/js/lib/knockout.components.js' )

        .sass(assetPath + '/scss/zawntech-wordpress-helpers.scss', assetPath + '/css/zawntech-wordpress-helpers.css');
});
