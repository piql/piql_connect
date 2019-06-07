const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .js(['node_modules/fine-uploader-wrappers/azure.js', 'node_modules/fine-uploader-wrappers/base-wrapper.js', 'node_modules/fine-uploader-wrappers/callback-names.js', 'node_modules/fine-uploader-wrappers/callback-proxy.js', 'node_modules/fine-uploader-wrappers/traditional.js', 'node_modules/fine-uploader/fine-uploader/fine-uploader.js'], 'public/js/fine-uploader.js')
    .sass('resources/sass/app.scss', 'public/css')
    .copyDirectory('resources/images/', 'public/images');


mix.styles([
    'node_modules/fine-uploader/fine-uploader/fine-uploader.css',
    'node_modules/fine-uploader/fine-uploader/fine-uploader-gallery.css'
], 'public/css/fine-uploader.css');
