/**
 * How to use:
 * a) add class 'sorter' to <table>
 * b) table must have <tbody> element
 * c) every <tr> must have data-id attribute with its ID
 * d) 'handle_sort' is link for sorting - it must be provided from the latte template (it's link is generated from nette)
 */
(function ($, window, document) {
    $(function () {
        bindTableSorter();
    });


    var bindTableSorter = function(){
        // Return a helper with preserved width of cells
        var fix_helper = function (e, ui) {
            ui.children().each(function () {
                $(this).width($(this).width());
            });
            return ui;
        };
        $(".sorter").each(function(){
            var $sorter_tbody = $(this).find('tbody');
            $sorter_tbody.sortable({
                update: function() {
                    $.get(handle_sort, {'sort': $sorter_tbody.sortable('toArray', { attribute: 'data-id' })});
                },
                helper: fix_helper
            }).disableSelection();
        });

    };

}(window.jQuery, window, document));



