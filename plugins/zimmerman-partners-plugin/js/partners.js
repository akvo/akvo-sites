
	// var partnerIcon = null;

	// function sort_obj_into_array(obj){

	// 	var arr = [];
	// 	for (var key in obj) arr.push([key, obj[key]]);

	// 	arr.sort(function(a, b) {
	// 	    a = a[1];
	// 	    b = b[1];

	// 	    return a < b ? -1 : (a > b ? 1 : 0);
	// 	});

	// 	return arr;
	// }
    
	
	



	

	// function zz_obj_to_arr(myObj){
	// 	var array = jQuery.map(myObj, function(value, index) {
	// 	    return [index];
	// 	});

	// 	return array;
	// }

	// function zz_obj_name_to_arr(myObj){
	// 	var array = jQuery.map(myObj, function(value, index) {
	// 	    return [value];
	// 	});

	// 	return array;
	// }


	

	

	// jQuery('.zz-partner-option-type').click(function($){
	// 	var key = $(this).attr("value");
	// 	var value = $(this).attr("name");

	// 	current_selection.types[key] = value;
	// 	refresh_filters();
	// 	get_partners();
	// 	$(this).val(0);
	// });


	
	

	// function show_partners_in_list(){

	// 	jQuery("#zz-partner-list").html("");
		
	// 	jQuery.getJSON( zz_partners_base_url + "/wp-admin/admin-ajax.php", {
	// 		action: "zz_partners_list",
 //            types: zz_obj_to_arr(current_selection.types),
 //            countries: zz_obj_to_arr(current_selection.countries),
 //            activities: zz_obj_to_arr(current_selection.activities),
 //            geo: zz_obj_to_arr(current_selection.geo),
 //            themes: zz_obj_to_arr(current_selection.themes),
 //            cities: zz_obj_to_arr(current_selection.cities)
 //        }, function(data){

 //        	var partner_counter = 0;

	// 		// set new markers
	// 		jQuery.each(data, function( index, value ) {		

	// 			partner_counter++;

	// 			var single_partner_listview = "";
	// 			single_partner_listview += '<div class="zz-partner-list-item partner-pos-'+partner_counter+'">';

	// 			single_partner_listview += '<div class="zz-partner-list-item-title">';
	// 			single_partner_listview += value.partner_title;
	// 			single_partner_listview += '</div>';

	// 			single_partner_listview += '<div class="zz-partner-list-item-main_info">';

	// 			single_partner_listview += value.partner_type+'<br>'+value.partner_city+', '+value.partner_country+'<br>&nbsp;<br><a target="_blank" href="'+value.partner_website+'">website</a><br><a href="mailto:'+value.partner_email+'">email</a>';

	// 			single_partner_listview += '</div>';
	// 			single_partner_listview += '</div>';

	// 			jQuery("#zz-partner-list").append(single_partner_listview);

	// 			if (partner_counter > 2){
	// 				partner_counter = 0;
	// 			}
	// 		});

	// 		if (zz_obj_to_arr(current_selection.cities).length > 0){

	// 		    jQuery("#zz-partner-list").show(200, function(){
	// 		    	jQuery('html,body').animate({
	// 					scrollTop: jQuery("#zz-partner-list").offset().top - 300
	// 				});
	// 		    });
	// 		}		
	// 	});
	// }

	// jQuery("#zz-partners-show-organisation-in-list").click(function(e){
	// 	e.preventDefault();
	// 	hide_show_list();
	// });

	// function hide_show_list(){
	// 	var list = jQuery("#zz-partner-list");
	// 	if (list.is(':visible')){
	// 		jQuery("#zz-partners-show-organisation-in-list-text").text("Show partners in list");
	// 	} else {
	// 		jQuery("#zz-partners-show-organisation-in-list-text").text("Hide partners");
	// 	}
	// 	jQuery("#zz-partner-list").toggle(500);
		

	// }

	
	// jQuery(".zz-partner-option-default").click(function($){

	// 	$(this).next().toggle(300);
	// 	$(this).toggleClass("zz-partner-option-default-active");
		
	// });

	// jQuery( "#zz-partner-search-input" ).focusout(function($) {
	// 	$("#zz-partner-search-filters").animate({
	//     	'min-height': 0
	//     }, 1000);
	// })
	// .focusin(function($) {
	//     $("#zz-partner-search-filters").animate({
	//     	'min-height': 240
	//     }, 1000);
	// });

	// jQuery('#zz-partner-search-input').autocomplete({

 //        source: function( request, response ) {
 //            jQuery.getJSON( zz_partners_base_url + "/wp-admin/admin-ajax.php", {
 //                search: request.term,
 //                action: "zz_partners_search_ajax"
 //            }, function(data){
 //                response(data.suggestions)
 //            } );
 //        },
 //        minLength: 1,
 //        select: function( event, ui ) {

 //        	current_selection.search = ui.item.label;

	// 		refresh_filters();
	// 		get_partners();
	// 		jQuery('#zz-partner-search-input').val("");

 //            return false;
 //        }
 //    });






jQuery( '.zz-filter-selection' ).change(function() {
	
	var key = jQuery(this).val();
	var value = jQuery("option:selected", this).text();
	var filter_type = jQuery(this).data("filter");
	if (!zz_filter.in_filter(filter_type, key)){
		zz_organisationlist.selection[filter_type].push({"id": key, "name":value});
	}
	jQuery(this).val(0);

	zz_filter.save();
});


function OipaSelection(main, has_default_reporter){
	this.cities = [];
	this.countries = [];
	this.activities = [];
	this.themes = [];
	this.geographical_focus = [];
	this.types = [];
	this.query = "";
}


function OipaMap(){
	this.map = null;
	this.selection = null;
	this.basemap = "zimmerman2014.hmpkg505";
	this.tl = null;
	this.markers = null;


	this.init = function(){

		map = L.map('zz-partner-map', {
			fullscreenControl: true,
		    attributionControl: false, 
		    scrollWheelZoom: false,
		    zoom: 3,
		    minZoom: 2,
		    maxZoom:6,
		    continuousWorld: 'true',
		    zoomControl: false
		}).setView([10.505, 25.09], 3);

		this.map = map;

		partnerIcon = L.icon({
		    iconUrl: zz_partner_base_url + "images/partner-icon.png",
		    iconSize:     [24, 34], // size of the icon
		    iconAnchor:   [12, 34], // point of the icon which will correspond to marker's location
		    popupAnchor:  [118, 50] // point from which the popup should open relative to the iconAnchor
		});

		new L.Control.Zoom({ position: 'bottomright' }).addTo(map);

		this.tl = L.tileLayer('https://{s}.tiles.mapbox.com/v3/'+this.basemap+'/{z}/{x}/{y}.png', {
			maxZoom: 12
		}).addTo(this.map);


		this.markers = L.layerGroup();
		this.markers.addTo(this.map);

		jQuery(".leaflet-control-fullscreen-button").html("Full screen");

		this.map.on('fullscreenchange', function () {
		    if (map.isFullscreen()) {
		        jQuery(".leaflet-control-fullscreen-button").html("Exit full screen");
		        jQuery(".leaflet-control-fullscreen-button").css("width", "120px");
		    } else {
		        jQuery(".leaflet-control-fullscreen-button").html("Full screen");
		        jQuery(".leaflet-control-fullscreen-button").css("width", "90px");
		    }
		});
		
		this.refresh();
	}

	this.refresh = function(data){


		if (!data){

			// get url
			var url = this.get_url();

			// get data
			this.get_data(url);
			
		} else {
			// show data
			this.show_data_on_map(data);
		}

	};

	this.get_url = function(){

		var str_cities = zz_get_parameters_from_selection(this.selection.cities);
		var str_countries = zz_get_parameters_from_selection(this.selection.countries);
		var str_activities = zz_get_parameters_from_selection(this.selection.activities);
		var str_themes = zz_get_parameters_from_selection(this.selection.themes);
		var str_geographical_focus = zz_get_parameters_from_selection(this.selection.geographical_focus);
		var str_types = zz_get_parameters_from_selection(this.selection.types);
		var str_query = this.selection.query;
		return zz_partners_base_url + "/wp-admin/admin-ajax.php?action=zz_partners_map_ajax&cities=" + str_cities + "&countries=" + str_countries + "&activities=" + str_activities + "&themes=" + str_themes + "&geographical_focus=" + str_geographical_focus + "&types=" + str_types + "&query=" + str_query;
	};

	this.get_data = function(url){

		var thismap = this;
		jQuery.ajax({
			type: 'GET',
			url: url,
			dataType: 'json',
			success: function(data){
				thismap.refresh(data);
			}
		});
	};
	
	
	this.delete_markers = function(){
		for (var i = 0; i < this.markers.length; i++) {
			this.map.removeLayer(this.markers[i]);
		}
	};

	this.get_markers_bounds = function(){

		var minlat = 0;
		var maxlat = 0;
		var minlng = 0;
		var maxlng = 0;
		var first = true;

		jQuery.each(this.markers._layers, function( index, value ) {

		  curlat = value._origLatlng.lat;
		  curlng = value._origLatlng.lng;

		  if (first){
				minlat = curlat;
				maxlat = curlat;
				minlng = curlng;
				maxlng = curlng;
			}

			if (curlat < minlat){
				minlat = curlat;
			}
			if (curlat > maxlat){
				maxlat = curlat;
			}
			if (curlng < minlng){
				minlng = curlng;
			}
			if (curlng > maxlng){
				maxlng = curlng;
			}

			first = false;
		});

		return [[minlat, minlng],[maxlat, maxlng]];
	}

	this.show_data_on_map = function(data){

    	// remove old markers
    	this.markers.clearLayers();

    	var thismap = this;

		// set new markers
		jQuery.each(data, function( index, value ) {
			console.log(typeof(value[0].multiple));
			if (typeof(value.multiple) === "undefined"){
				// add marker for org

				if (value[0].partner_longitude != "" && value[0].partner_latitude != ""){

					var marker = L.marker([value[0].partner_latitude, value[0].partner_longitude], {
						icon: partnerIcon, 
    					bounceOnAdd: true, 
    					bounceOnAddOptions: {duration: 500, height: 100}, 
  					}).bindPopup('<b>'+value[0].partner_title+'</b>'+value[0].partner_type+'<br>'+value[0].partner_city+', '+value[0].partner_country+'<br>&nbsp;<br><a target="_blank" href="'+value[0].partner_website+'">website</a><br><a href="mailto:'+value[0].partner_email+'">email</a>');
			  		thismap.markers.addLayer(marker);
				}

			} else {
				console.log("multiple");
				// add marker for multi orgs -> go to list view
				if (value[0].partner_longitude != "" && value[0].partner_latitude != ""){

					var marker = L.marker([value[0].partner_latitude, value[0].partner_longitude], {
						icon: partnerIcon, 
    					bounceOnAdd: true, 
    					bounceOnAddOptions: {duration: 500, height: 100}, 
  					}).bindPopup('<b>Multiple partners</b>'+value[0].partner_city+', '+value[0].partner_country+'<br><a class="zz-partner-map-pol-list-click" data-id="'+value[0].partner_city_slug+'" href="#" data-name="'+value[0].partner_city+'">View in list</a>');
			  		thismap.markers.addLayer(marker);
				}
			}
			
		});
		
		if (data.length == 0){
			// var types_string = "";
			// var types = zz_obj_name_to_arr(current_selection.types);
			// if (types.length == 1){
			// 	types_string = " with type " + types.join(", ");
			// } else {
			// 	types_string = " with types " + types.join(", ");
			// }

			// var countries_string = "";
			// var countries = zz_obj_name_to_arr(current_selection.countries);
			// if (countries.length == 1){
			// 	countries_string = " in country " + countries.join(", ");
			// } else {
			// 	countries_string = " in countries " + countries.join(", ");
			// }

			jQuery("#zz-partner-no-partners-found").text("No partners found in the current selection.");
			jQuery("#zz-partner-no-partners-found").delay(400).fadeIn(500).delay(2000).fadeOut();;

			thismap.map.setView([10.505, 25.09], 3);
		} else {
			var bounds = this.get_markers_bounds();
			thismap.map.fitBounds(bounds);
		}

		this.load_map_listeners();
		
	};

	this.load_map_listeners = function(){
		var thismap = this;
		jQuery(".leaflet-clickable").click(function(){

			jQuery(".zz-partner-map-pol-list-click").click(function(e){
				e.preventDefault();
				var city_slug = jQuery(this).data("id");
				var city_name = jQuery(this).data("name");
				if (!zz_filter.in_filter("countries", city_slug)){
					thismap.selection.countries.push({"id": city_slug, "name":city_name});
				}
				zz_filter.save();

				jQuery('html,body').animate({
				   scrollTop: jQuery("#oipa-information").offset().top - 80
				});
			});

		});
	};


	this.change_basemap = function(basemap_id){
		this.tl._url = "https://{s}.tiles.mapbox.com/v3/"+basemap_id+"/{z}/{x}/{y}.png";
		this.tl.redraw();
	};

	this.zoom_on_dom = function(curelem){
		var latitude = curelem.getAttribute("latitude");
		var longitude = curelem.getAttribute("longitude");
		var country_id = curelem.getAttribute("name");
		var country_name = curelem.getAttribute("country_name");

		this.map.setView([latitude, longitude], 6);
		Oipa.mainSelection.countries.push({"id": country_id, "name": country_name});
		Oipa.refresh_maps();
		Oipa.refresh_lists();
	};
}



function OipaList(){

	this.offset = 0;
	this.limit = 10;
	this.amount = 0;
	this.order_by = null;
	this.order_asc_desc = null;
	this.selection = new OipaSelection();
	this.api_resource = "activity-list";
	this.keep_offset = false;

	this.list_div = "#oipa-list";
	this.pagination_div = "#oipa-list-pagination";
	this.activity_count_div = ".oipa-list-amount";

	this.init = function(){
		var thislist = this;
		// init pagination
		jQuery(this.pagination_div).bootpag({
		   total: 5,
		   page: 1,
		   maxVisible: 6
		}).on('page', function(event, num){
			thislist.go_to_page(num);
		});

		this.load_listeners();
		this.update_pagination();

		jQuery("#oipa-list-search").keyup(function() {
			if (jQuery(this).val().length == 0){
				thislist.selection.query = "";
				zz_filter.save();
			} 
		});

		jQuery("#oipa-information form").submit(function(e){
			e.preventDefault();
			thislist.selection.query = jQuery("#oipa-list-search").val();
			thislist.offset = 0;
			zz_filter.save();
		});
	}


	this.refresh = function(data){
		
		if (!data){
			// get URL
			var url = this.get_url();

			// get data
			this.get_data(url);

		} else {
			// set amount of results
			this.update_list(data);
			this.update_pagination(data);
			this.load_listeners();
			
		}
	}

	this.reset_pars = function(){
		this.selection.query = "";
		this.offset = 0;
		this.limit = 10;
		this.amount = 0;
		this.order_by = null;
		this.order_asc_desc = null;
		this.refresh();
	}

	this.get_url = function(){
		var str_cities = zz_get_parameters_from_selection(this.selection.cities);
		var str_countries = zz_get_parameters_from_selection(this.selection.countries);
		var str_activities = zz_get_parameters_from_selection(this.selection.activities);
		var str_themes = zz_get_parameters_from_selection(this.selection.themes);
		var str_geographical_focus = zz_get_parameters_from_selection(this.selection.geographical_focus);
		var str_types = zz_get_parameters_from_selection(this.selection.types);
		var str_limit = this.limit;
		var str_offset = this.offset;

		if(this.keep_offset){
			var str_offset = this.offset;
		} else {
			var str_offset = 0;
		}

		var str_query = this.selection.query;

		return zz_partners_base_url + "/wp-admin/admin-ajax.php?action=zz_partners_list&cities=" + str_cities + "&countries=" + str_countries + "&activities=" + str_activities + "&themes=" + str_themes + "&geographical_focus=" + str_geographical_focus + "&types=" + str_types + "&offset=" + str_offset + "&limit=" + str_limit + "&query=" + str_query;
	};

	this.get_data = function(url){

		var curlist = this;
		jQuery.ajax({
			type: 'GET',
			url: url,
			dataType: 'html',
			success: function(data){
				curlist.refresh(data);
			}
		});
	};

	this.update_list = function(data){
		// generate list html and add to this.list_div
		jQuery(this.list_div).html(data);
	};

	this.load_listeners = function(){

		jQuery(".zz-list-more-details").click(function(e){
			e.preventDefault();
			jQuery(".zz-partner-more-details").hide();
			var partner_id = jQuery(this).data("id");
			if (jQuery(".zz-partner-more-details[data-id='"+partner_id+"']").hasClass("active")){
				jQuery(".zz-partner-more-details[data-id='"+partner_id+"']").hide(500);
 				jQuery(".zz-partner-more-details").removeClass("active");
				jQuery(this).text("More details");
			} else {
				jQuery(".zz-partner-more-details[data-id='"+partner_id+"']").show(500);
				jQuery(".zz-partner-more-details[data-id='"+partner_id+"']").addClass("active");
				jQuery(this).text("Hide details");
			}
			

			
		});

		var thislist = this;
		jQuery("#oipa-list-count").html(jQuery(thislist.list_div + " " + thislist.activity_count_div).val());
	};


	this.update_pagination = function(data){

		var total = jQuery(this.list_div + " " + this.activity_count_div).val();
		this.amount = total;
		var total_pages = Math.ceil(this.amount / this.limit);
		var current_page = Math.ceil(this.offset / this.limit) + 1;
		jQuery(this.pagination_div).bootpag({total: total_pages});
	};

	this.go_to_page = function(page_id){
		this.offset = (page_id * this.limit) - this.limit;
		this.keep_offset = true;
		this.refresh();
	};

	this.export = function(format){

		var url = this.get_url();
		url = url.replace("format=json", "format=" + format);
		url_splitted = url.split("?");
		url = site_url + ajax_path + "/" + this.api_resource + "/?" + url_splitted[1];

		jQuery("#ExportListHiddenWrapper").remove();

		iframe = document.createElement('a');
        iframe.id = "ExportListHiddenWrapper";
        iframe.style.display = 'none';
        document.body.appendChild(iframe);

        var export_func_url = base_url + "/" + theme_path + "/export.php?path=" + encodeURIComponent(url);

        jQuery("#ExportListHiddenWrapper").attr("href", export_func_url);
        jQuery("#ExportListHiddenWrapper").attr("target", "_blank");
        jQuery("#ExportListHiddenWrapper").bind('click', function() {
			window.location.href = this.href;
			return false;
		});
        jQuery("#ExportListHiddenWrapper").click();
		jQuery("#download-dialog").toggle();
	}

}





function OipaFilters(){

	this.selection = null;

	this.save = function(){
		
		zz_filter.refresh_filters();

		// reload maps
		zz_map.refresh();

		// reload lists
		zz_organisationlist.refresh();
	};


	this.refresh_filters = function(){

		// // remove old filters
		jQuery('.zz-partner-filter-selected').html("");

		// fill filters
		for(var i = 0; i < this.selection.countries.length; i++){
		  jQuery('#zz-partner-countries-filters .zz-partner-filter-selected').append('<div class="zz-partner-filter-single-selected">'+this.selection.countries[i]["name"]+'<a id="countries--'+this.selection.countries[i]["id"]+'" class="zz-filter-glyphicon glyphicon glyphicon-remove"></a></div>');
		}

		for(var i = 0; i < this.selection.cities.length; i++){
		  jQuery('#zz-partner-cities-filters .zz-partner-filter-selected').append('<div class="zz-partner-filter-single-selected">'+this.selection.cities[i]["name"]+'<a id="cities--'+this.selection.cities[i]["id"]+'" class="zz-filter-glyphicon glyphicon glyphicon-remove"></a></div>');
		}

		for(var i = 0; i < this.selection.activities.length; i++){
		  jQuery('#zz-partner-activities-filters .zz-partner-filter-selected').append('<div class="zz-partner-filter-single-selected">'+this.selection.activities[i]["name"]+'<a id="activities--'+this.selection.activities[i]["id"]+'" class="zz-filter-glyphicon glyphicon glyphicon-remove"></a></div>');
		}

		for(var i = 0; i < this.selection.themes.length; i++){
		  jQuery('#zz-partner-themes-filters .zz-partner-filter-selected').append('<div class="zz-partner-filter-single-selected">'+this.selection.themes[i]["name"]+'<a id="themes--'+this.selection.themes[i]["id"]+'" class="zz-filter-glyphicon glyphicon glyphicon-remove"></a></div>');
		}

		for(var i = 0; i < this.selection.geographical_focus.length; i++){
		  jQuery('#zz-partner-geographical_focus-filters .zz-partner-filter-selected').append('<div class="zz-partner-filter-single-selected">'+this.selection.geographical_focus[i]["name"]+'<a id="geographical_focus--'+this.selection.geographical_focus[i]["id"]+'" class="zz-filter-glyphicon glyphicon glyphicon-remove"></a></div>');
		}

		for(var i = 0; i < this.selection.types.length; i++){
		  jQuery('#zz-partner-types-filters .zz-partner-filter-selected').append('<div class="zz-partner-filter-single-selected">'+this.selection.types[i]["name"]+'<a id="types--'+this.selection.types[i]["id"]+'" class="zz-filter-glyphicon glyphicon glyphicon-remove"></a></div>');
		}


        this.load_listeners();
	}

	this.load_listeners = function(){

		jQuery('.zz-partner-filter-single-selected a').click(function(){

			var id = jQuery(this).attr('id');
			var type_and_key = id.split('--');
			var type = type_and_key[0];
			var clicked_value = type_and_key[1];



			for (i = 0; i < zz_filter.selection[type].length; i++) { 
			    
				if (zz_filter.selection[type][i].id == clicked_value){

					zz_filter.selection[type].splice(i, 1);
					break;
				}
			}

			jQuery(this).parent().remove();
			zz_filter.save();
		});
		
	}

	this.reset_filters = function(){

		// REMOVE YELLOW BOXES
		jQuery(".zz-partner-filter-selected").remove();
		this.selection.cities = [];
		this.selection.countries = [];
		this.selection.activities = [];
		this.selection.themes = [];
		this.selection.geographical_focus = [];
		this.selection.type = [];
		this.selection.query = "";
		this.save();
	}

	this.in_filter = function(type, id){

		for (i = 0; i < this.selection[type].length; i++) { 
			if (this.selection[type][i].id == id){
				return true;
			}
		}

		return false;
	}

}



function zz_get_parameters_from_selection(arr){

	dlmtr = ",";
	var str = '';

	if(arr.length > 0){

		str = '';
		for(var i = 0; i < arr.length; i++){
			str += arr[i].id + dlmtr;
		}
		str = str.substring(0, str.length-1);
	}

	return str;
}

jQuery("#zz-partner-map .listview, #zz-partner-list-view-buttons .listview").click(function(e){
	jQuery("#zz-partner-map-wrapper").hide('swing');
	jQuery("#zz-partner-list-view-buttons").show('swing');
});

jQuery("#zz-partner-map .mapview, #zz-partner-list-view-buttons .mapview").click(function(e){
	jQuery("#zz-partner-map-wrapper").show('swing', function(){
		zz_map.map._onResize(); 
	});
	jQuery("#zz-partner-list-view-buttons").hide('swing');
});





var zz_selection = new OipaSelection();
var zz_organisationlist = new OipaList();
zz_organisationlist.selection = zz_selection;
zz_organisationlist.init();
var zz_filter = new OipaFilters();
zz_filter.selection = zz_selection;
var zz_map = new OipaMap();
zz_map.selection = zz_selection;
zz_map.init();
// zz_filter.save();

