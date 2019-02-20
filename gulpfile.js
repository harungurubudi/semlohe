var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var minifyjs = require('gulp-js-minify');

gulp.task('js', function() {
  return gulp.src([
    './source/js/admin/**/*.js'
  ])
  .pipe(concat('index.bundle.js'))
  .pipe(uglify().on('error', function(e){
    console.log(e);
  }))
  .pipe(minifyjs())
  .pipe(gulp.dest('./public/assets/admin/js'));
});

gulp.task('admin', ['js'], function() {
  gulp.watch('source/js/**/*.js', ['js']);  
});