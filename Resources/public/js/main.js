$(document).ready(function() {

    /**
     * Submit the form when a form field has changed.
     */
    $('.js-submit-on-change').change(function() {
        $(this).closest('form').submit();
    });

    /**
     * Set some display data on the delete confirmation modal
     */
    $('.js-delete-confirm').click(function () {
        $(".modal-body .modal-name").html($(this).attr('data-name'));
        $(".modal .modal-confirm-button").attr('href', $(this).attr('data-href'));
    });

    /**
     * Confirm batch process
     *
     * This opens a modal before actually batch processing.
     */
    $('.js-batch-confirm').click(function() {

        var form = $(this).closest('form');
        var action = form.find('.js-batch-action').val();
        
        // If no action is passed, there's no 
        if (action) {
            var selected = form.find('.js-batch-action option:selected');
            var modal = form.find('.batch-modal');

            // Get all selected rows
            var checkedValues = form.find('.batch-select:checked').map(function() {
                return this.value;
            }).get();

            // If no items were selected, return
            if (checkedValues.length < 1) {
                return;
            }

            // Set some display data on the model
            modal.find('.batch-action').each(function() {
                $(this).html(selected.attr('data-action'));
            });
            modal.find('.batch-count').html(checkedValues.length);

            // Set the action on the form, so it knows where to submit to.
            form.get(0).setAttribute('action', action);

            // Open the modal
            $('#batch-modal').modal({show:true});
        }
    });
    
    /**
     * Submit the batch form
     *
     * This is called from a button inside the batch confirmation modal
     *
     * @param  {Event} e
     */
    $('.js-batch-start').click(function(e) {
        e.preventDefault();

        var form = $(this).closest('form');
        form.submit();
    });
});
