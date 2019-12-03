const gulp = require('gulp');

module.exports.copyVendor = async () => {
    const vendorFolders = {
        'js': [
            'node_modules/jquery/dist/jquery.min.js',
            'node_modules/bootstrap/dist/js/bootstrap.min.js',
        ],
        'css': [
            'node_modules/font-awesome/css/font-awesome.min.css',
        ],
        'fonts': [
            'node_modules/font-awesome/fonts/*'
        ],
    };

    const folderTasks = [];
    for (let folder in vendorFolders) {
        const sources = vendorFolders[folder];

        folderTasks.push(gulp.src(sources).pipe(gulp.dest('www/vendor/' + folder)));
    }

    await Promise.all(folderTasks);
};
