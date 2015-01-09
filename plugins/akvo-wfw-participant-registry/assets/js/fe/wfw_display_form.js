jQuery(document).ready(function($) {

	$('input[name=radioParticipatedBefore]').on('change', function () {
		var oInvokedElement = $(this);

		var sValue = oInvokedElement.val();
		switch (sValue) {
			case 'yes':
				// Do nothing...
				break;
			case 'no':
				// Uncheck all related checkboxes
				$('#checkboxParticipatedLastYear').prop('checked', false);
				$('#checkboxParticipatedYearBeforeLast').prop('checked', false);
				$('#checkboxParticipatedBeforeTheLastTwoYears').prop('checked', false);
				break;
		}

	});

	$('#checkboxParticipatedLastYear, #checkboxParticipatedYearBeforeLast, #checkboxParticipatedBeforeTheLastTwoYears').on('change', function () {
		var oInvokedElement = $(this);

		var bChecked = oInvokedElement.prop('checked');
		if (bChecked) {
			// Set related radio option to 'yes'
			$('#radioParticipatedBefore-yes').prop('checked', true);
		}

	});

});

