jQuery(document).ready(function($){

	var Home = {

		oResponseContainer: $('#iDivResponseContainer'),

		init: function () {

			var oHome = this;

			oHome._initFilterElements();
			oHome._initBindings();
//			console.log(oHome._fetchCurrentUrlParts('url'));

		},

		_initFilterElements: function () {

			var sBatch = oPageConfig.batch;

			$('#selectBatchFilter option').filter(function () {
				var mValue = $.trim($(this).val());
				var bMatch = (mValue == sBatch);
				return bMatch;
			}).prop('selected', true);

		},

		_initBindings: function () {

			var oHome = this;

			$('#selectBatchFilter').on('change', function () {

				var oInvokedElement = $(this);
				var sBatch = oInvokedElement.val();

				var sNewUrl = oHome._redirect('batch', sBatch);
//				console.log(sNewUrl);

			});

			$('#iButtonDownloadList').on('click', function () {

				var oInvokedElement = $(this);
				var sBatch = oInvokedElement.data('batch');

				// Do AJAX
				$.ajax({
					cache: false,
					data: {
						action: 'AkvoWvWParticipantRegistry_export',
						batch: sBatch
					},
					dataType: 'json',
					type: 'POST',
					url: ajaxurl
				}).done(function (oResponse, sTextStatus, oJqXhr) {

					var sStatus = '';
					var sMessage = '';

					if (typeof oResponse == 'undefined') {
						Kwg_Tbs_Interface.alert(oHome.oResponseContainer, 'An unexpected error has occurred. No response received for the request.', 'warning');
					} else {
						if (typeof oResponse.status == 'undefined') {
							Kwg_Tbs_Interface.alert(oHome.oResponseContainer, 'An unexpected error has occurred. Response status has not been provided.', 'warning');
						} else {
							sStatus = oResponse.status;

							switch (sStatus) {
								case 'successful':
									sMessage = oResponse.message;
									var sLink = oResponse.link;
									var sHtmlContent = sMessage + '<br><br>' + '<a class="btn btn-success btn-block btn-large" href="' + sLink + '" target="_blank"><i class="icon icon-download"></i>&nbsp;Download List</a>';
									$('#iDivModalDownloadList').find('.modal-body p').empty().html(sHtmlContent);
									$('#iDivModalDownloadList').modal();

									break;
								case 'failed':
									sMessage = oResponse.message;
									Kwg_Tbs_Interface.alert(oHome.oResponseContainer, sMessage, 'error');
									break;
								default:
									Kwg_Tbs_Interface.alert(oHome.oResponseContainer, 'An unexpected response has been returned.', 'warning');
									break;
							}
						}
					}

				}).fail(function (jqXHR, textStatus, errorThrown) {

				});

			});

		},

		_redirect: function (sParameterName, sParameterValue, bReset) {

			var oHome = this;

			var sCurrentBaseUrl = 'http://' + oHome._fetchCurrentUrlParts('hostname') + oHome._fetchCurrentUrlParts('pathname');
			var sCurrentUrl = '';
			switch (sParameterName) {
				case 'batch':
					sCurrentUrl = sCurrentBaseUrl
						+ '?page=' + oPageConfig.page
						+ '&batch=' + sParameterValue
//						+ '&order_by_column=' + oPageConfig.order_by_column
//						+ '&order_by_direction=' + oPageConfig.order_by_dir	ection
					;
					break;
				case 'order_by_column':
					break;
				case 'order_by_direction':
					break;
			}

			window.location = sCurrentUrl;
			return sCurrentUrl;





			if (typeof bReset == 'undefined') {
				bReset = false;
			}

			var sCurrentUrl = oHome._fetchCurrentUrlParts('href');
			var oCurrentUrlParameters = oHome._fetchCurrentUrlParts('params');
			var oNewUrlParameters = {};

			if (bReset) {

				oNewUrlParameters = oNewParameters;

			} else {

				oNewUrlParameters = $.extend({}, oCurrentUrlParameters, oNewParameters);

			}

			var aNewUrlParameters = [];
			for (var iNewUrlParameterKey in oNewUrlParameters) {
//				aNewUrlParameters.
			}
console.log(sCurrentUrl);
			var sNewUrl = sCurrentUrl + '?' + aNewUrlParameters.join('&');
			return sNewUrl;

		},

		_fetchCurrentUrlParts: function (sContext) {

			var oHome = this;
			var oLocation = $(location);
			//console.log(oLocation);

			var mReturn = null;

			switch (sContext) {
				case 'hash':
					mReturn = oLocation.attr('hash');
					break;
				case 'host':
					mReturn = oLocation.attr('host');
					break;
				case 'hostname':
					mReturn = oLocation.attr('hostname');
					break;
				case 'href':
				case 'url':
					mReturn = oLocation.attr('href');
					break;
				case 'pathname':
					mReturn = oLocation.attr('pathname');
					break;
				case 'port':
					mReturn = oLocation.attr('port');
					break;
				case 'protocol':
					mReturn = oLocation.attr('protocol');
					break;
				case 'search':
				case 'params':

					var sSearch = oLocation.attr('search');
					var aSearch = [];
					var oSearch = {};
					if (sSearch.length > 1) {
						sSearch = sSearch.substring(1);

						aSearch = sSearch.split('&');

						for (var iSearchIndex in aSearch) {
							var sSearchPart = aSearch[iSearchIndex];
							var aSearchPart = sSearchPart.split('=');

							oSearch[aSearchPart[0]] = aSearchPart[1];

						}

					}

					mReturn = oSearch;

					break;

			}

			return mReturn;

		}

	};

	Home.init();

//	$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
//
//	if (typeof(KwgPressFormRedirect) != 'undefined') {
//
//		Kwg_Timer.setTimeout('install_successful_redirect', function(){
//			window.location = KwgPressFormRedirect;
//		}, 3000);
//
//	}
//
//	$('.cFormErrorPopover').popover({
//		html: true,
//		placement: "bottom",
//		trigger: "hover",
//		title: '<span class="text-error"><i class="icon-warning-sign"></i>&nbsp;<strong>Error!</strong></span>'
//	});






});