const gulp = require('gulp');

const sassTasks = require('./sass.gulp');
const vendorTasks = require('./vendor.gulp');

gulp.task('sass', sassTasks.sass);

gulp.task('sass:watch', () => {
    return gulp.watch([
        'app/**/*.scss',
        'sass/**/*.scss'
    ], gulp.task('sass'));
});

gulp.task('vendor', vendorTasks.copyVendor);
