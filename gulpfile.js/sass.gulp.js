const gulp = require('gulp');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');

module.exports.sass = async () => {
    await gulp.src(['sass/*.sass', 'sass/*.scss'])
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('www/dist/css/', {sourcemaps: true}))
};
