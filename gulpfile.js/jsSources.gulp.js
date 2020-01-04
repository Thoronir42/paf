const gulp = require('gulp');
const uglify = require('gulp-uglify');
const sourcemaps = require('gulp-sourcemaps');
const concat = require('gulp-concat');

const pipeline = require('readable-stream').pipeline;


module.exports.sources = [
    'jsSrc/**/*',
    'app/Modules/CmsModule/js/**/*.js',
];

module.exports.concatSources = () => {
    return pipeline(
        gulp.src(module.exports.sources),
        sourcemaps.init(),
        concat('main.js'),
        // uglify(),
        sourcemaps.write('./'),
        gulp.dest('www/dist/js')
    );
};
