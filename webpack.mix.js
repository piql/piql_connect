const mix = require('laravel-mix');

mix.webpackConfig({
    resolve: {
        alias: {
            '@js': path.resolve(__dirname, 'resources/js/'),
            '@views': path.resolve(__dirname, 'resources/js/views/'),
            '@components': path.resolve(__dirname, 'resources/js/components/'),
            '@mixins': path.resolve(__dirname, 'resources/js/mixins/'),
        }
    }
});

mix.js('resources/js/app.js', 'public/js', 'node_modules/video.js/dist/video.min.js')
    .extract()
    .sourceMaps();

mix.copyDirectory('resources/images/', 'public/images')
    .copyDirectory('resources/fa/webfonts/', 'public/webfonts')
    .copy('resources/fonts/Agenda/*', 'public/fonts')
    .copy('resources/fonts/Open_Sans/*', 'public/fonts');

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
            'node_modules/bootstrap-vue/dist/bootstrap-vue.min.css',
            'node_modules/bootstrap-vue/dist/bootstrap-vue-icons.min.css',
            'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css',
            'node_modules/bootstrap-select/dist/css/bootstrap-select.min.css',
            'resources/fa/css/all.css',
            'node_modules/fine-uploader/fine-uploader/fine-uploader.css',
            'node_modules/fine-uploader/fine-uploader/fine-uploader-gallery.css',
            'node_modules/vue-resize/dist/vue-resize.css',
			'node_modules/video.js/dist/video-js.min.css'
], 'public/css/vendor.css');

mix.autoload({
        'jquery': ['$', 'window.jQuery', 'jQuery'],
        'vue': ['Vue','window.Vue'],
});

mix.browserSync({
        proxy: '127.0.0.1',
        files: ['resources/**/*.*']
});

