jQuery(document).ready(function() {

	var oLatLng = new google.maps.LatLng(sCenterPointX, sCenterPointY);
	var oMap;

	function initialize() {
		var aMapOptions = {
			zoom: iZoomFactor,
			center: oLatLng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		oMap = new google.maps.Map(document.getElementById('iDivMapCanvas'),
			aMapOptions);

		for(var i=0; i<aGeocodes.length; i++){
			var aCodes = aGeocodes[i];
			var sName = aCodes['name'];
			var oGeoCodes = new google.maps.LatLng(aCodes['lat'], aCodes['long']);

			var oMarker=new google.maps.Marker({
				position: oGeoCodes,
				map: oMap,
				title: sName
			});

		var oInfowindow = new google.maps.InfoWindow();
		google.maps.event.addListener(oMarker, 'click', (function(oMarker, sName) {
					return function() {
						oInfowindow.setContent(sName);
						oInfowindow.open(oMap, oMarker);
					}
				})(oMarker, sName));

		}

	}

	google.maps.event.addDomListener(window, 'load', initialize);
});