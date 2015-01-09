var Kwg_Tbs_Interface = {

	alert: function (oContainer, sContent, sContext) {

		var sAlertContext = '';

		switch (sContext) {
			case 'success':
			case 'alert-success':
				sAlertContext = ' alert-success'
				break;
			case 'error':
			case 'alert-error':
				sAlertContext = ' alert-error'
				break;
			case 'warning':
			case 'alert-warning':
				sAlertContext = '';
				break;
			case 'info':
			case 'alert-info':
				sAlertContext = ' alert-info'
				break;
		}

		var sAlertHtml = '';
		sAlertHtml += '<div class="alert' + sAlertContext + '">';
		sAlertHtml += '<button type="button" class="close" data-dismiss="alert">&times;</button>';
		sAlertHtml += sContent;
		sAlertHtml += '</div>';

		oContainer.html(sAlertHtml);
	}

};