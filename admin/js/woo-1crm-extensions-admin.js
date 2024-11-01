(function ($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
	 *
	 * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
	 *
	 * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

    jQuery(document).ready(function () {

        var fieldname;

        jQuery(".customfieldsdivs > .newfield").click(function () {
            var this_id = $(this).attr('id');

            fieldname = jQuery('<div>' +
                '<label for="fieldname">Feld Name:</label><input type="text" name="fieldname" required><br />' +
                '<label for="fieldid">Feld ID im 1CRM:</label><input type="text" name="fieldid" required><br />' +
                '<label for="fieldtype">Feld Typ:</label><select class="fieldtype" name="fieldtype"><option value="input" selected>input</option><option value="dropdown">DropDown</option><option value="checkbox">Checkbox</option><option value="textarea">Textarea</option></select><br />' +
                '<label for="fieldvalues" style="display: none;">Feld Values:</label><input type="text" name="fieldvalues" style="display: none;">' +
                '<input type="button" class="deleteButtons donotserialize button action" value="- löschen">' +
                '</div>');

            jQuery('#' + this_id).parent().append(fieldname);

        });

        $(document).on('click', '.deleteButtons', function () {
            jQuery(this).parent().remove();
        });


        jQuery("#addfieldsform").submit(function () {
            jQuery('#billingcustomfieldsarray').val(JSON.stringify(jQuery('#billingcustomfieldsarraydiv :input').not('.donotserialize').serializeArray()));
            jQuery('#shippingcustomfieldsarray').val(JSON.stringify(jQuery('#shippingcustomfieldsarraydiv :input').not('.donotserialize').serializeArray()));
            jQuery('#accountcustomfieldsarray').val(JSON.stringify(jQuery('#accountcustomfieldsarraydiv :input').not('.donotserialize').serializeArray()));
            jQuery('#ordercustomfieldsarray').val(JSON.stringify(jQuery('#ordercustomfieldsarraydiv :input').not('.donotserialize').serializeArray()));
        });


        jQuery(".customfieldsdivs").on('change', '.fieldtype', function () {
            if (jQuery(this).find(":selected").val() == "dropdown") {
                jQuery(this).siblings('label[for="fieldvalues"]').text("DropDown Inhalte (separiert durch Semikolon):");
                jQuery(this).siblings('label[for="fieldvalues"]').show();
                jQuery(this).siblings('input[name="fieldvalues"]').show();
                jQuery(this).siblings('input[name="fieldvalues"]').attr("required", true);
            }

            if (jQuery(this).find(":selected").val() == "checkbox") {
                jQuery(this).siblings('label[for="fieldvalues"]').css('display', 'none');
                jQuery(this).siblings('input[name="fieldvalues"]').css('display', 'none');
                jQuery(this).siblings('input[name="fieldvalues"]').attr("required", false);
            }

            if (jQuery(this).find(":selected").val() == "textarea") {
                jQuery(this).siblings('label[for="fieldvalues"]').css('display', 'none');
                jQuery(this).siblings('input[name="fieldvalues"]').css('display', 'none');
                jQuery(this).siblings('input[name="fieldvalues"]').attr("required", false);
            }

            if (jQuery(this).find(":selected").val() == "input") {
                jQuery(this).siblings('label[for="fieldvalues"]').css('display', 'none');
                jQuery(this).siblings('input[name="fieldvalues"]').css('display', 'none');
                jQuery(this).siblings('input[name="fieldvalues"]').attr("required", false);
            }

        });

    });


})(jQuery);
