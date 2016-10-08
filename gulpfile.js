var gulp = require('gulp');
var babelify = require('babelify');
var browserify = require('browserify');
var source = require('vinyl-source-stream');
var livereload = require('gulp-livereload');
var uglify = require('gulp-uglify');

gulp.task('build', function () {
	return browserify('./src/js/main.js')
		.transform('babelify', {presets: ['es2015', 'react']})
		.bundle()
		.pipe(source('main.min.js'))
		.pipe(gulp.dest('./backend/js/'))
		.pipe(livereload());
});

gulp.task('min', function () {
	gulp.src('./backend/js/main.min.js')
		.pipe(uglify())
		.pipe(gulp.dest('./backend/js/'))
});

gulp
  // moves source files to backend
  .task('copy', function(){
    gulp
      .src('src/index.html')
      .pipe(gulp.dest('backend'));
   
     gulp
      .src('src/assets/**/*.*')
      .pipe(gulp.dest('backend/assets'));
   
     gulp
      .src('src/img/**/*.*')
      .pipe(gulp.dest('backend/img'));
  })
 
 
  // build the application
  // .task('default', ['browserify','copy', 'connect', 'open'])
  .task('default', ['build', 'copy'])

  // watch for source changes
  .task('watch', ['default'], function(){
    // livereload.listen();
    gulp.watch('src/**/*.*', ['default']);
  });