const gulp = require('gulp');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const livereload = require('gulp-livereload');
const sassOptions = {
    errLogToConsole: true,
    outputStyle: 'compressed'
};
const autoprefixerOptions = {
    browsers: ['last 5 versions', '> 5%', 'Firefox ESR']
};

gulp.task('scss', function () {
    gulp.src('./assets/scss/**/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass(sassOptions).on('error', sass.logError))
        .pipe(autoprefixer(autoprefixerOptions))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('./assets/css'))
        .pipe(livereload());
});

gulp.task('js', function () {
    gulp.src('./assets/js/admin/*.js')
        .pipe(concat('admin.js'))
        .pipe(gulp.dest('./assets/js'))
        .pipe(concat('admin.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./assets/js'))
        .pipe(livereload());

    gulp.src('./assets/js/public/*.js')
        .pipe(concat('form.js'))
        .pipe(gulp.dest('./assets/js'))
        .pipe(concat('form.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./assets/js'))
        .pipe(livereload());

    gulp.src('./assets/js/polyfill/*.js')
        .pipe(concat('polyfill.js'))
        .pipe(gulp.dest('./assets/js'))
        .pipe(concat('polyfill.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./assets/js'))
        .pipe(livereload());
});

gulp.task('watch', function () {
    livereload.listen();
    gulp.watch('./assets/scss/**/*.scss', ['scss']);
    gulp.watch('./assets/js/public/*.js', ['js']);
    gulp.watch('./assets/js/admin/*.js', ['js']);
});

gulp.task('default', ['scss', 'js', 'watch']);
