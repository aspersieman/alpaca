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

mix.disableSuccessNotifications();

mix.webpackConfig({
  resolve: {
    alias: {
      '@': __dirname + '/resources/js'
    }
  }
});

mix
  .js('resources/js/app.js', 'public/js')
  .copyDirectory('resources/img', 'public/img')
  .copyDirectory('resources/fonts', 'public/fonts')
  .sass('resources/sass/index.scss', 'public/css')
  .extract(['vue', 'vue-router', 'vue-shortkey', 'moment', 'axios', 'lodash', 'dropzone'])
  .sourceMaps();
