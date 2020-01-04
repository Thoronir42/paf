const gulp = require('gulp');

const sassTasks = require('./sass.gulp');
const jsSrc = require('./jsSources.gulp');
const vendorTasks = require('./vendor.gulp');

gulp.task('sass', sassTasks.sass);

gulp.task('sass:watch', () => {
    return gulp.watch([
        'app/**/*.scss',
        'sass/**/*.scss'
    ], gulp.task('sass'));
});

gulp.task('js', jsSrc.concatSources);

gulp.task('js:watch', () => {
    return gulp.watch(
        jsSrc.sources,
        gulp.task('js'));
});

gulp.task('vendor', vendorTasks.copyVendor);
