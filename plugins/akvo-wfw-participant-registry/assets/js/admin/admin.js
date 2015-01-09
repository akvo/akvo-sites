jQuery(document).ready(function($){

	if (typeof(AkvoWfWParticipantRegistryRedirect) != 'undefined') {

		setTimeout(function(){
			window.location = 'admin.php?page='+AkvoWfWParticipantRegistryRedirect;
		}, 3000);

	}

	if ($('.cDatePicker').length > 0) {
		var oDatePicker = $('.cDatePicker').datepicker({
            format: 'dd-mm-yyyy'
        });
	}

	$('.cFormErrorPopover').popover({
		html: true,
		placement: "bottom",
		trigger: "hover",
		title: '<span class="text-error"><i class="icon-warning-sign"></i>&nbsp;<strong>Error!</strong></span>'
	});


	var oRegistrantDeleteForm = $('#iFormRegistrantDelete');

	if (oRegistrantDeleteForm.length > 0) {

		$('#iButtonRemoveRegistrantConfirmation').click(function(){
			oRegistrantDeleteForm.submit();
		});

	}

});