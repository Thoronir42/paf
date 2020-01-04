$(document).ready(function () {
    naja.initialize();

    bsCustomFileInput.init();

    initTags();

    initEditable();

    initSelect2();

    initDatePicker();

});

function initTags() {
    var options = {
        tags: true
    };
    $('.tags').select2(options)
}

function initEditable() {
    var options = {
        success: function (response, newValue) {
            if (response.status === 'error') {
                return response.message;
            }
        },
        name: 'value',
        mode: 'inline'

    };
    $('a.editable').each(function () {
        var $this = $(this);
        var specOpts = $.extend({}, options);
        specOpts.url = function (params) {
            var url = $this.data('url-set');
            var values = {},
                name = $this.data('value-name');
            values[name] = params['value'];

            return $.post(url, values);
        };

        $this.editable(specOpts);
    });
}

function initSelect2() {
    $('select:not(.no-select2)').each(function () {
        var $element = $(this);
        $element.select2({
            theme: 'bootstrap4',
            width: 'style',
            placeholder: $(this).attr('placeholder'),
            allowClear: Boolean($(this).data('allow-clear')),
        });
    })
}

function initDatePicker() {
    $('.td-wrapper').each(function (a, el) {
        var $el = $(el);

        var options = {
            debug: true,
        };
        Object.assign(options, $el.data());

        $el.datetimepicker(options);
    });
}
