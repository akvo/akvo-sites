// JavaScript Document
jQuery(document).ready(function(){
	jQuery('#snsf-checkbox').click(function(){	
		if(jQuery('#snsf-checkbox').is(':checked')){
			jQuery('#snsf-submit-button').removeAttr('disabled');
		}
		else{
			jQuery('#snsf-submit-button').attr('disabled','disabled');
		}
		
		
		
	});
	
	
	
});