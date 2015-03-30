var gulp = require('gulp');
var plugins = require('gulp-load-plugins')();

var out = './dist';

gulp.task('styles', function () {
    gulp.src([
        'bower_components/datetimepicker/jquery.datetimepicker.css',
        'node_modules/select2/select2.css',
        'assets/styles/main.scss'
    ])
        .pipe(plugins.sourcemaps.init())
        .pipe(plugins.sass())
        .pipe(plugins.autoprefixer())
        .pipe(plugins.concat('fieldwork.css'))
        .pipe(plugins.sourcemaps.write())
        .pipe(gulp.dest(out));
});

gulp.task('scripts', function () {
    gulp.src([
        'bower_components/jquery-maskedinputs/dist/jquery.maskedinput.min.js',
        'bower_components/datetimepicker/jquery.datetimepicker.js',

        'node_modules/underscore/underscore-min.js',
        'node_modules/sweetalert/lib/sweet-alert.min.js',
        'node_modules/select2/select2.js',

        'assets/js/fieldwork-tooltips.js',
        'assets/js/fieldwork.js'
    ])
        .pipe(plugins.uglifyjs('fieldwork.min.js', {
            outSourceMap: true
        }))
        .pipe(gulp.dest(out));
});

gulp.task('copy-select2-assets', function () {
    return gulp.src('node_modules/select2/*.png')
        .pipe(gulp.dest(out));
});

gulp.task('default', ['styles', 'scripts', 'copy-select2-assets']);