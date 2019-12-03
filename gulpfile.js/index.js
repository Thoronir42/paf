const gulp = require('gulp');

const sassTasks = require('./sass.gulp');
const vendorTasks = require('./vendor.gulp');

gulp.task('sass', sassTasks.sass);

gulp.task('vendor', vendorTasks.copyVendor);
