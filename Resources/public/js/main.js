$(document).ready(function() {
    $('.js-limit-select').change(function() {
        $(this).closest('form').submit();
    });
});
