$(function () {
    $.nette.init();
});

$(document).ready( function () {
    initConfirmation();

    initSortable();

    initTags();

    initEditable();
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

function initSortable() {
    var $sortables = $('.sortable');

    var options = {
        update: function () {
            $.get(handle_sort, {'sort': $sortables.sortable('toArray', { attribute: 'data-id' })});
        }
    };

    $sortables.sortable(options);
}

function initTags(){
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
        specOpts.url = function(params) {
            var url = $this.data('url-set');
            var values = {},
                name = $this.data('value-name');
            values[name] = params['value'];

            return $.post(url, values);
        };

        $this.editable(specOpts);
    });
}
