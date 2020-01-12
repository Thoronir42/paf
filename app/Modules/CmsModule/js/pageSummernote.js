$(function () {

    function CmsPageController(/**jQuery*/ controlsEl, /**jQuery*/ contentEl)
    {
        this.$controlsEl = controlsEl;
        this.$contentEl = contentEl;
        this.controlName = contentEl.data('controlName');
    }

    CmsPageController.prototype.initSummernote = function () {
        this.$contentEl.summernote({});

        this.$controlsEl.addClass('initialized');
        this.$controlsEl.addClass('active');
    };

    CmsPageController.prototype.save = function () {
        let markup = this.$contentEl.summernote('code');
        let saveUrl = this.$contentEl.data('saveUrl');
        console.log(saveUrl, markup);

        let data = {};
        data[this.controlName + '-content'] = markup;

        naja.makeRequest('POST', saveUrl, data, {history: false})
            .then(function (result) {
                console.log(result);
            })
            .catch(function (error) {
                console.error(error);
            });

        this.$contentEl.summernote('destroy');

        this.$controlsEl.removeClass('initialized');
        this.$controlsEl.removeClass('active');
    };

    let $body = $('body');

    $body.on('click', '.cms-page-controls .editable-init', function () {
        let ctrl = getControllerForButton($(this));
        ctrl.initSummernote();
    });

    $body.on('click', '.cms-page-controls .editable-save', function () {
        let ctrl = getControllerForButton($(this));
        ctrl.save();
    });

    /**
     *
     * @param {jQuery} $btn
     * @returns {CmsPageController}
     */
    function getControllerForButton($btn)
    {
        let $controlsEl = $btn.closest('.cms-page-controls');

        let ctrl = $controlsEl.data('cmsPageController');
        if (!ctrl) {
            let controlsId = $controlsEl.attr('id');

            let contentId = controlsId.replace('controls', 'content');

            let $contentEl = $('#' + contentId);

            ctrl = new CmsPageController($controlsEl, $contentEl);
            $controlsEl.data('cmsPageController', ctrl);
        }

        return ctrl;
    }
});
