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

const sassFileList = [
    'build/scss/common',
    'build/scss/vendor',
    'pages/index'
];

const jsFileList = [
    'build/js/common',
    'pages/index',
];

mix.options({
  clearConsole: true,
});

mix.setPublicPath('assets/')

jsFileList.forEach(fileName => mix.js(`./${fileName}.js`, 'js'));

sassFileList.forEach(
    fileName => mix.sass(`./${fileName}.scss`, 'css')
        .options({processCssUrls: false})
);

mix.sourceMaps(true, 'source-map');

mix.version()
