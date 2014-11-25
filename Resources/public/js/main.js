$(document).ready(function() {
    $('.js-submit-on-change').change(function() {
        $(this).closest('form').submit();
    });

    $('.js-delete-confirm').click(function () {
         $(".modal-body .modal-name").html($(this).attr('data-name'));
         $(".modal .modal-confirm-button").attr('href', $(this).attr('data-href'));
    });

    $('.js-batch-process').click(function(e) {
        e.preventDefault();

        var form = $(this).closest('form');

        var action = form.find('.js-batch-action').val();

        var checkedValues = form.find('.batch-select:checked').map(function() {
            return this.value;
        }).get();
    });
});
