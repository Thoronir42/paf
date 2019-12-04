$(document).ready(function () {
    $.nette.init();

    // initConfirmation();

    initTags();

    initEditable();

    initSelect2();

    initDatePicker();
    
});

function initConfirmation() {
    var options = {
        title: "Wowie, opravdu?",
        singleton: true,
        popout: true,
        placement: 'bottom',

        btnOkLabel: "Jasně",
        btnCancelLabel: "Ne-e"
    };
    $('[data-toggle=confirmation]').confirmation(options);
}

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
        console.log($element)
    })
}

function initDatePicker() {
    $('input.date').each(function () {
        $(this).datetimepicker({
            weekstart: 1,
            todayHighlight: true
        });
    });
}



