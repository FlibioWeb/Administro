var gulp  = require('gulp'),
    sass = require('gulp-sass'),
    cssnano = require('gulp-cssnano'),
    eol = require('gulp-eol'),

    input  = '../scss/**/*.scss',

    output = '../../../../assets/admin/';

gulp.task('default', ['build', 'watch']);

gulp.task('build', function() {
  return gulp.src(input)
    .pipe(sass())
    .pipe(cssnano({zindex: false}))
    .pipe(eol())
    .pipe(gulp.dest(output));
});

gulp.task('watch', function() {
  gulp.watch(input, ['build']);
});
