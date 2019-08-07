const mix = require('laravel-mix');
const WebpackShellPlugin = require('webpack-shell-plugin');

mix.webpackConfig({
    plugins: [
	/* Handles conversion of translations to .js on build */
        new WebpackShellPlugin({ onBuildStart: ['php artisan vue-i18n:generate'], onBuildEnd: [] }),
    ],
    module: {
        rules: [
            {
                // Matches all PHP or JSON files in `resources/lang` directory.
                test: /resources(\|\/)lang.+\.(php|json)$/,
                loader: 'babel'
            },
        ],
    },
});

mix.js('resources/js/app.js', 'public/js')
    .js(['node_modules/fine-uploader-wrappers/azure.js', 'node_modules/fine-uploader-wrappers/base-wrapper.js', 'node_modules/fine-uploader-wrappers/callback-names.js', 'node_modules/fine-uploader-wrappers/callback-proxy.js', 'node_modules/fine-uploader-wrappers/traditional.js', 'node_modules/fine-uploader/fine-uploader/fine-uploader.js'], 'public/js/fine-uploader.js')
    .sass('resources/sass/app.scss', 'public/css')
    .js('node_modules/bootstrap-select/dist/js/bootstrap-select.min.js', 'public/js')
    .styles('node_modules/bootstrap-select/dist/css/bootstrap-select.min.css', 'public/css/bootstrap-select.min.css')
    .js('node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js', 'public/js')
    .styles('node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css', 'public/css/bootstrap-datepicker3.min.css')
    .copyDirectory('resources/images/', 'public/images');


mix.styles([
    'node_modules/fine-uploader/fine-uploader/fine-uploader.css',
    'node_modules/fine-uploader/fine-uploader/fine-uploader-gallery.css'
], 'public/css/fine-uploader.css');
