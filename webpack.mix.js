const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js');

mix.js([
        'node_modules/fine-uploader-wrappers/base-wrapper.js', 
        'node_modules/fine-uploader-wrappers/callback-names.js', 
        'node_modules/fine-uploader-wrappers/callback-proxy.js', 
        'node_modules/fine-uploader-wrappers/traditional.js', 
        'node_modules/fine-uploader/fine-uploader/fine-uploader.js'], 
    'public/js/vendor.js')
    .sourceMaps();


mix.copyDirectory('resources/images/', 'public/images')
    .copyDirectory('resources/fa/webfonts/', 'public/webfonts')
    .copy('resources/fonts/Agenda/*', 'public/fonts')
    .copy('resources/fonts/Open_Sans/*', 'public/fonts')
    .copy('resources/css/bootstrap.min.css.map', 'public/css');

mix.sass('resources/sass/app.scss', 'public/css')
      .options({
            processCssUrls: false,
            uglify: {
                parallel: 4,
                uglifyOptions: {
                    mangle: true,
                    compress: true,
                }
            }
      });

mix.styles([
            'resources/css/bootstrap.min.css',
            'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css',
            'node_modules/bootstrap-select/dist/css/bootstrap-select.min.css',
            'resources/fa/css/all.css',
            'node_modules/fine-uploader/fine-uploader/fine-uploader.css',
            'node_modules/fine-uploader/fine-uploader/fine-uploader-gallery.css',
], 'public/css/vendor.css').sourceMaps();

mix.browserSync({
	proxy: 'connect.local',
	files: ['resources/**/*.*']
});

