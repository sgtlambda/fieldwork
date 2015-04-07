var gulp = require('gulp');
var sourcemaps = require('gulp-sourcemaps');
var autoprefixer = require('gulp-autoprefixer');
var concat = require('gulp-concat');
var sass = require('gulp-sass');
var uglify = require('gulp-uglifyjs');

var out = './dist';

gulp.task('styles', function () {
    gulp.src([
        'bower_components/datetimepicker/jquery.datetimepicker.css',
        'node_modules/select2/select2.css',
        'assets/styles/main.scss'
    ])
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(autoprefixer())
        .pipe(concat('fieldwork.css'))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest(out));
});

gulp.task('scripts', function () {
    gulp.src([
        'bower_components/jquery-maskedinputs/dist/jquery.maskedinput.min.js',
        'bower_components/datetimepicker/jquery.datetimepicker.js',

        'node_modules/lodash/index.js',
        'node_modules/sweetalert/lib/sweet-alert.min.js',
        'node_modules/select2/select2.js',

        'assets/js/fieldwork-tooltips.js',
        'assets/js/fieldwork.js'
    ])
        .pipe(uglify('fieldwork.min.js', {
            outSourceMap: true
        }))
        .pipe(gulp.dest(out));
});

gulp.task('copy-select2-assets', function () {
    return gulp.src('node_modules/select2/*.png')
        .pipe(gulp.dest(out));
});

gulp.task('default', ['styles', 'scripts', 'copy-select2-assets']);