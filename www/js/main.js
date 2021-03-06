$(document).ready(function () {
    hideNoScript();
    naja.initialize();

    bsCustomFileInput.init();
    // initConfirmation();

    initTags();

    initEditable();

    initSelect2();

    initDatePicker();
    initAjax();
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
    })
}

function initDatePicker() {
    $('.td-wrapper').each(function (a, el) {
        let $el = $(el);

        let options = {
            debug: true,
        };
        Object.assign(options, $el.data());

        $el.datetimepicker(options);
    });
}

function initAjax() {
    let submitters = window.document.querySelectorAll('.submit-on-change');
    submitters.forEach((submitter) => {
        $(submitter).on('change', () => {
            let parentForm = submitter.form;
            if (!parentForm) {
                console.warn("Element", submitter, "does not belong to a form");
                return;
            }

            let result = naja.uiHandler.submitForm(parentForm);
            console.log(result);
        });
    });
}

function hideNoScript() {
    window.document.querySelectorAll('.d-noscript-hidden').forEach((element) => {
        let inputContainer = element.closest('.form-group');
        if (inputContainer) {
            inputContainer.classList.add('d-none');
        } else {
            element.classList.add('d-none');
        }
    });
}

