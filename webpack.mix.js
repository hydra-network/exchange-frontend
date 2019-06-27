let mix = require('laravel-mix');

theme = [
    'resources/assets/js/theme/jquery.min.js',
    'resources/assets/js/theme/jquery-ui.min.js',
    'resources/assets/js/theme/bootstrap.min.js',
    'resources/assets/js/theme/jquery.bootstrap.wizard.min.js',
    'resources/assets/js/theme/bootstrap-table.js'
];

mix
    .scripts(theme, 'public/js/theme.js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .js('resources/assets/js/app.js', 'public/js')

    .version();

