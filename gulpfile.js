var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */
/*
elixir(function(mix) {
    mix.sass('app.scss');
});
*/
/*
 See dev dependencies https://gist.github.com/isimmons/8927890
 Compiles sass to compressed css with autoprefixing
 Compiles coffee to javascript
 Livereloads on changes to coffee, sass, and blade templates
 Runs PHPUnit tests
 Watches sass, coffee, blade, and phpunit
 Default tasks sass, coffee, phpunit, watch
 */
var gulp = require('gulp');
var path = require('path');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');



gulp.task('compress', function() {

    gulp.src('./public/assets/js/**/*.js')
        .pipe(uglify())
        .pipe(concat('main.min.js'))
        .pipe(gulp.dest('./public/dist/js'))
});


gulp.task('watch', function () {

    gulp.watch('public/assets/js/**/*.js', ['compress']);
});


/* Default Task */

gulp.task('default', ['compress', 'watch']);
