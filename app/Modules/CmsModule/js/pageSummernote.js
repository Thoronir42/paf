$(function () {
    $('body').on('click', '.cms-page-controls .editable-toggle', function () {
        var $controlsEl = $(this).closest('.cms-page-controls');
        var ctrlId = $controlsEl.attr('id');

        var contentId = ctrlId.replace('controls', 'content');

        var $contentEl = $('#' + contentId);
        if ($controlsEl.hasClass('active')) {
            $contentEl.summernote('destroy');
        } else {
            $contentEl.summernote({
                airMode: true,
            });
        }

        $controlsEl.toggleClass('active');
    });
});
