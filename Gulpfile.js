var gulp = require('gulp');
var plugins = require('gulp-load-plugins')();

var out = './dist';

gulp.task('styles', function () {
    gulp.src([
        'bower_components/datetimepicker/jquery.datetimepicker.css',
        'assets/styles/main.scss'
    ])
        .pipe(plugins.sourcemaps.init())
        .pipe(plugins.sass())
        .pipe(plugins.concat('fieldwork.css'))
        .pipe(plugins.sourcemaps.write())
        .pipe(gulp.dest(out));
});

gulp.task('scripts', function () {
    gulp.src([
        'assets/js/fieldwork-tooltips.js',
        'assets/js/fieldwork.js'
    ])
        .pipe(plugins.uglifyjs('fieldwork.min.js', {
            outSourceMap: true
        }))
        .pipe(gulp.dest(out));
});

gulp.task('default', ['styles', 'scripts']);