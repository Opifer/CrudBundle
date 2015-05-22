$(document).ready(function() {

    /**
     * Submit the form when a form field has changed.
     */
    $('.js-submit-on-change').change(function() {
        $(this).closest('form').submit();
    });



    /**
     * Select all rows for batch processing
     */
    $('.js-batch-select-all').click(function() {
        if(this.checked) {
            $('.batch-select').each(function() {
                this.checked = true;
            });
        }else{
            $('.batch-select').each(function() {
                this.checked = false;
            });         
        }
    });

    /**
     * Set some display data on the delete confirmation modal
     */
    $('.js-delete-confirm').click(function () {
        $(".modal-body").html(Translator.trans('modal.delete.message', { "d" : $(this).attr('data-name') }));
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
            var dataAction = selected.attr('data-action');
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
                $(".modal-body").html(Translator.transChoice('modal.batch.message', checkedValues.length, { "d" : checkedValues.length, "s": dataAction }));
                $(this).html(dataAction);
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
