const gulp = require('gulp');

module.exports.copyVendor = async () => {
    const vendorFolders = {
        'js': [
            'node_modules/jquery/dist/jquery.min.js',
            'node_modules/bootstrap/dist/js/bootstrap.min.js',
            'node_modules/select2/dist/js/select2.full.min.js',
            'node_modules/X-editable/dist/bootstrap3-editable/js/bootstrap-editable.js',
        ],
        'css': [
            'node_modules/font-awesome/css/font-awesome.min.css',
            'node_modules/select2/dist/css/select2.min.css',
            'node_modules/@ttskch/select2-bootstrap4-theme/dist/select2-bootstrap4.css',
            'node_modules/X-editable/dist/bootstrap3-editable/css/bootstrap-editable.css',
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
