$(document).on('change', '.btn-file :file', function () {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
});

$(document).ready(function () {
    $('.btn-file :file').on('fileselect', function (event, numFiles, label) {

        var $label = $(this).parents('.input-group').find('.file-label'),
            log = numFiles > 1 ? numFiles + ' files selected' : label;

        if ($label.length) {
            $label.text(log);
        }
    });
});
