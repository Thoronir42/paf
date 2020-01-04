const gulp = require('gulp');

const dependencies = [
    {
        src: 'node_modules/jquery/dist/*',
        dst: 'jQuery',
    },
    {
        src: [
            'node_modules/font-awesome/css/*',
            'node_modules/font-awesome/fonts/*',
        ],
        srcOptions: {
            base: 'node_modules/font-awesome',
        },
        dst: 'font-awesome',
    },
    {
        src: 'node_modules/moment/min/**',
        dst: 'moment',
    },
    {
        src: 'node_modules/popper.js/dist/umd/*.js',
        dst: 'popper.js',
    },
    {
        src: 'node_modules/bootstrap/dist/**/*',
        dst: 'bootstrap',
    },
    {
        src: 'node_modules/bs-custom-file-input/dist/*',
        dst: 'bootstrap/bs-custom-file-input',
    },
    {
        src: 'node_modules/X-editable/dist/bootstrap3-editable/**/*',
        dst: 'bootstrap/x-editable',
    },
    {
        src: 'node_modules/tempusdominus-bootstrap-4/build/**/*',
        dst: 'bootstrap/tempusdominus'
    },
    {
        src: 'node_modules/select2/dist/**/*',
        dst: 'select2',
    },
    {
        src: 'node_modules/@ttskch/select2-bootstrap4-theme/dist/*',
        dst: 'select2/bootstrap4-theme',
    },
    {
        src: 'node_modules/nette-forms/src/assets/*',
        dst: 'nette/forms',
    },
    {
        src: 'node_modules/naja/dist/*',
        dst: 'nette/naja',
    },
    {
        src: [
            "node_modules/summernote/dist/font/*",
            "node_modules/summernote/dist/lang/*",
            "node_modules/summernote/dist/summernote-bs4*",
        ],
        srcOptions: {
            base: 'node_modules/summernote/dist',
        },
        dst: 'summernote',
    },
];


module.exports.copyVendor = async () => {
    const folderTasks = [];

    dependencies.forEach((dependency) => {
        let task = gulp.src(dependency.src, dependency.srcOptions)
            .pipe(gulp.dest('www/vendor/' + dependency.dst));

        folderTasks.push(task);
    });

    await Promise.all(folderTasks);
};
