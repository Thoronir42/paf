$(function () {
    $.nette.init();
});

$(document).ready( function () {
    initConfirmation();

    initSortable();

    initTags();
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
