const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.browserSync('lamoid.local');
mix.disableSuccessNotifications();
mix
    .js('resources/js/app.js', 'public/js')
    .vue()
    .copyDirectory('resources/img', 'public/img')
    .copyDirectory('resources/fonts', 'public/fonts')
    .sass('resources/sass/index.scss', 'public/css')
    .sourceMaps();

mix.webpackConfig({
  resolve: {
    alias: {
      '@': __dirname + '/resources/js'
    }
  }
});
