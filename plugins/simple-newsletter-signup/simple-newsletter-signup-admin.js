// JavaScript Document

//admin js

// on check #snsf-all-checkboxes", select all check boxes
jQuery(document).ready(function(){
    var newValue;
	jQuery('#snsf-action-form').change(function () {
		 newValue = jQuery('#snsf-action-form').val();
			jQuery('#snsf-the-do-action').val(newValue);
    });
	
	jQuery('#snsf-all-checkboxes').click(function () {
        jQuery('.all-checkable').attr('checked', this.checked);
    });
// on change update the form with action
    
	
	jQuery('#snsf-perform-action').click(function(){

			jQuery('#snsf-actions-form').submit();

});

});

