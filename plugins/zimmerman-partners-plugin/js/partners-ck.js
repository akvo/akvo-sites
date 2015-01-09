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
function OipaSelection(e,t){this.cities=[];this.countries=[];this.activities=[];this.themes=[];this.geographical_focus=[];this.types=[];this.query=""}function OipaMap(){this.map=null;this.selection=null;this.basemap="zimmerman2014.hmpkg505";this.tl=null;this.markers=null;this.init=function(){map=L.map("zz-partner-map",{fullscreenControl:!0,attributionControl:!1,scrollWheelZoom:!1,zoom:3,minZoom:2,maxZoom:6,continuousWorld:"true",zoomControl:!1}).setView([10.505,25.09],3);this.map=map;partnerIcon=L.icon({iconUrl:zz_partner_base_url+"images/partner-icon.png",iconSize:[24,34],iconAnchor:[12,34],popupAnchor:[118,50]});(new L.Control.Zoom({position:"bottomright"})).addTo(map);this.tl=L.tileLayer("https://{s}.tiles.mapbox.com/v3/"+this.basemap+"/{z}/{x}/{y}.png",{maxZoom:12}).addTo(this.map);this.markers=L.layerGroup();this.markers.addTo(this.map);jQuery(".leaflet-control-fullscreen-button").html("Full screen");this.map.on("fullscreenchange",function(){if(map.isFullscreen()){jQuery(".leaflet-control-fullscreen-button").html("Exit full screen");jQuery(".leaflet-control-fullscreen-button").css("width","120px")}else{jQuery(".leaflet-control-fullscreen-button").html("Full screen");jQuery(".leaflet-control-fullscreen-button").css("width","90px")}});this.refresh()};this.refresh=function(e){if(!e){var t=this.get_url();this.get_data(t)}else this.show_data_on_map(e)};this.get_url=function(){var e=zz_get_parameters_from_selection(this.selection.cities),t=zz_get_parameters_from_selection(this.selection.countries),n=zz_get_parameters_from_selection(this.selection.activities),r=zz_get_parameters_from_selection(this.selection.themes),i=zz_get_parameters_from_selection(this.selection.geographical_focus),s=zz_get_parameters_from_selection(this.selection.types),o=this.selection.query;return zz_partners_base_url+"/wp-admin/admin-ajax.php?action=zz_partners_map_ajax&cities="+e+"&countries="+t+"&activities="+n+"&themes="+r+"&geographical_focus="+i+"&types="+s+"&query="+o};this.get_data=function(e){var t=this;jQuery.ajax({type:"GET",url:e,dataType:"json",success:function(e){t.refresh(e)}})};this.delete_markers=function(){for(var e=0;e<this.markers.length;e++)this.map.removeLayer(this.markers[e])};this.get_markers_bounds=function(){var e=0,t=0,n=0,r=0,i=!0;jQuery.each(this.markers._layers,function(s,o){curlat=o._origLatlng.lat;curlng=o._origLatlng.lng;if(i){e=curlat;t=curlat;n=curlng;r=curlng}curlat<e&&(e=curlat);curlat>t&&(t=curlat);curlng<n&&(n=curlng);curlng>r&&(r=curlng);i=!1});return[[e,n],[t,r]]};this.show_data_on_map=function(e){this.markers.clearLayers();var t=this;jQuery.each(e,function(e,n){if(n.length==1){if(n[0].partner_longitude!=""&&n[0].partner_latitude!=""){var r=L.marker([n[0].partner_latitude,n[0].partner_longitude],{icon:partnerIcon,bounceOnAdd:!0,bounceOnAddOptions:{duration:500,height:100}}).bindPopup("<b>"+n[0].partner_title+"</b>"+n[0].partner_type+"<br>"+n[0].partner_city+", "+n[0].partner_country+'<br>&nbsp;<br><a target="_blank" href="'+n[0].partner_website+'">website</a><br><a href="mailto:'+n[0].partner_email+'">email</a>');t.markers.addLayer(r)}}else if(n[0].partner_longitude!=""&&n[0].partner_latitude!=""){var r=L.marker([n[0].partner_latitude,n[0].partner_longitude],{icon:partnerIcon,bounceOnAdd:!0,bounceOnAddOptions:{duration:500,height:100}}).bindPopup("<b>Multiple partners</b>"+n[0].partner_city+", "+n[0].partner_country+'<br><a class="zz-partner-map-pol-list-click" data-id="'+n[0].partner_city_slug+'" href="#" data-name="'+n[0].partner_city+'">View in list</a>');t.markers.addLayer(r)}});if(e.length==0){jQuery("#zz-partner-no-partners-found").text("No partners found in the current selection.");jQuery("#zz-partner-no-partners-found").delay(400).fadeIn(500).delay(2e3).fadeOut();t.map.setView([10.505,25.09],3)}else{var n=this.get_markers_bounds();t.map.fitBounds(n)}this.load_map_listeners()};this.load_map_listeners=function(){var e=this;jQuery(".leaflet-clickable").click(function(){jQuery(".zz-partner-map-pol-list-click").click(function(t){t.preventDefault();var n=jQuery(this).data("id"),r=jQuery(this).data("name");zz_filter.in_filter("countries",n)||e.selection.countries.push({id:n,name:r});zz_filter.save();jQuery("html,body").animate({scrollTop:jQuery("#oipa-information").offset().top-80})})})};this.change_basemap=function(e){this.tl._url="https://{s}.tiles.mapbox.com/v3/"+e+"/{z}/{x}/{y}.png";this.tl.redraw()};this.zoom_on_dom=function(e){var t=e.getAttribute("latitude"),n=e.getAttribute("longitude"),r=e.getAttribute("name"),i=e.getAttribute("country_name");this.map.setView([t,n],6);Oipa.mainSelection.countries.push({id:r,name:i});Oipa.refresh_maps();Oipa.refresh_lists()}}function OipaList(){this.offset=0;this.limit=10;this.amount=0;this.order_by=null;this.order_asc_desc=null;this.selection=new OipaSelection;this.api_resource="activity-list";this.keep_offset=!1;this.list_div="#oipa-list";this.pagination_div="#oipa-list-pagination";this.activity_count_div=".oipa-list-amount";this.init=function(){var e=this;jQuery(this.pagination_div).bootpag({total:5,page:1,maxVisible:6}).on("page",function(t,n){e.go_to_page(n)});this.load_listeners();this.update_pagination();jQuery("#oipa-list-search").keyup(function(){if(jQuery(this).val().length==0){e.selection.query="";zz_filter.save()}});jQuery("#oipa-information form").submit(function(t){t.preventDefault();e.selection.query=jQuery("#oipa-list-search").val();zz_filter.save()})};this.refresh=function(e){if(!e){var t=this.get_url();this.get_data(t)}else{this.update_list(e);this.update_pagination(e);this.load_listeners()}};this.reset_pars=function(){this.selection.query="";this.offset=0;this.limit=10;this.amount=0;this.order_by=null;this.order_asc_desc=null;this.refresh()};this.get_url=function(){var e=zz_get_parameters_from_selection(this.selection.cities),t=zz_get_parameters_from_selection(this.selection.countries),n=zz_get_parameters_from_selection(this.selection.activities),r=zz_get_parameters_from_selection(this.selection.themes),i=zz_get_parameters_from_selection(this.selection.geographical_focus),s=zz_get_parameters_from_selection(this.selection.types),o=this.limit,u=this.offset;if(this.keep_offset)var u=this.offset;else var u=0;var a=this.selection.query;return zz_partners_base_url+"/wp-admin/admin-ajax.php?action=zz_partners_list&cities="+e+"&countries="+t+"&activities="+n+"&themes="+r+"&geographical_focus="+i+"&types="+s+"&offset="+u+"&limit="+o+"&query="+a};this.get_data=function(e){var t=this;jQuery.ajax({type:"GET",url:e,dataType:"html",success:function(e){t.refresh(e)}})};this.update_list=function(e){jQuery(this.list_div).html(e)};this.load_listeners=function(){jQuery(".zz-list-more-details").click(function(e){e.preventDefault();jQuery(".zz-partner-more-details").hide();var t=jQuery(this).data("id");if(jQuery(".zz-partner-more-details[data-id='"+t+"']").hasClass("active")){jQuery(".zz-partner-more-details[data-id='"+t+"']").hide(500);jQuery(".zz-partner-more-details").removeClass("active");jQuery(this).text("More details")}else{jQuery(".zz-partner-more-details[data-id='"+t+"']").show(500);jQuery(".zz-partner-more-details[data-id='"+t+"']").addClass("active");jQuery(this).text("Hide details")}});var e=this;jQuery("#oipa-list-count").html(jQuery(e.list_div+" "+e.activity_count_div).val())};this.update_pagination=function(e){var t=jQuery(this.list_div+" "+this.activity_count_div).val();this.amount=t;var n=Math.ceil(this.amount/this.limit),r=Math.ceil(this.offset/this.limit)+1;jQuery(this.pagination_div).bootpag({total:n})};this.go_to_page=function(e){this.offset=e*this.limit-this.limit;this.keep_offset=!0;this.refresh()};this.export=function(e){var t=this.get_url();t=t.replace("format=json","format="+e);url_splitted=t.split("?");t=site_url+ajax_path+"/"+this.api_resource+"/?"+url_splitted[1];jQuery("#ExportListHiddenWrapper").remove();iframe=document.createElement("a");iframe.id="ExportListHiddenWrapper";iframe.style.display="none";document.body.appendChild(iframe);var n=base_url+"/"+theme_path+"/export.php?path="+encodeURIComponent(t);jQuery("#ExportListHiddenWrapper").attr("href",n);jQuery("#ExportListHiddenWrapper").attr("target","_blank");jQuery("#ExportListHiddenWrapper").bind("click",function(){window.location.href=this.href;return!1});jQuery("#ExportListHiddenWrapper").click();jQuery("#download-dialog").toggle()}}function OipaFilters(){this.selection=null;this.save=function(){zz_filter.refresh_filters();zz_map.refresh();zz_organisationlist.refresh()};this.refresh_filters=function(){jQuery(".zz-partner-filter-selected").html("");for(var e=0;e<this.selection.countries.length;e++)jQuery("#zz-partner-countries-filters .zz-partner-filter-selected").append('<div class="zz-partner-filter-single-selected">'+this.selection.countries[e].name+'<a id="countries--'+this.selection.countries[e].id+'" class="zz-filter-glyphicon glyphicon glyphicon-remove"></a></div>');for(var e=0;e<this.selection.cities.length;e++)jQuery("#zz-partner-cities-filters .zz-partner-filter-selected").append('<div class="zz-partner-filter-single-selected">'+this.selection.cities[e].name+'<a id="cities--'+this.selection.cities[e].id+'" class="zz-filter-glyphicon glyphicon glyphicon-remove"></a></div>');for(var e=0;e<this.selection.activities.length;e++)jQuery("#zz-partner-activities-filters .zz-partner-filter-selected").append('<div class="zz-partner-filter-single-selected">'+this.selection.activities[e].name+'<a id="activities--'+this.selection.activities[e].id+'" class="zz-filter-glyphicon glyphicon glyphicon-remove"></a></div>');for(var e=0;e<this.selection.themes.length;e++)jQuery("#zz-partner-themes-filters .zz-partner-filter-selected").append('<div class="zz-partner-filter-single-selected">'+this.selection.themes[e].name+'<a id="themes--'+this.selection.themes[e].id+'" class="zz-filter-glyphicon glyphicon glyphicon-remove"></a></div>');for(var e=0;e<this.selection.geographical_focus.length;e++)jQuery("#zz-partner-geographical_focus-filters .zz-partner-filter-selected").append('<div class="zz-partner-filter-single-selected">'+this.selection.geographical_focus[e].name+'<a id="geographical_focus--'+this.selection.geographical_focus[e].id+'" class="zz-filter-glyphicon glyphicon glyphicon-remove"></a></div>');for(var e=0;e<this.selection.types.length;e++)jQuery("#zz-partner-types-filters .zz-partner-filter-selected").append('<div class="zz-partner-filter-single-selected">'+this.selection.types[e].name+'<a id="types--'+this.selection.types[e].id+'" class="zz-filter-glyphicon glyphicon glyphicon-remove"></a></div>');this.load_listeners()};this.load_listeners=function(){jQuery(".zz-partner-filter-single-selected a").click(function(){var e=jQuery(this).attr("id"),t=e.split("--"),n=t[0],r=t[1];for(i=0;i<zz_filter.selection[n].length;i++)if(zz_filter.selection[n][i].id==r){zz_filter.selection[n].splice(i,1);break}jQuery(this).parent().remove();zz_filter.save()})};this.reset_filters=function(){jQuery(".zz-partner-filter-selected").remove();this.selection.cities=[];this.selection.countries=[];this.selection.activities=[];this.selection.themes=[];this.selection.geographical_focus=[];this.selection.type=[];this.selection.query="";this.save()};this.in_filter=function(e,t){for(i=0;i<this.selection[e].length;i++)if(this.selection[e][i].id==t)return!0;return!1}}function zz_get_parameters_from_selection(e){dlmtr=",";var t="";if(e.length>0){t="";for(var n=0;n<e.length;n++)t+=e[n].id+dlmtr;t=t.substring(0,t.length-1)}return t}jQuery(".zz-filter-selection").change(function(){var e=jQuery(this).val(),t=jQuery("option:selected",this).text(),n=jQuery(this).data("filter");zz_filter.in_filter(n,e)||zz_organisationlist.selection[n].push({id:e,name:t});jQuery(this).val(0);zz_filter.save()});jQuery("#zz-partner-map .listview, #zz-partner-list-view-buttons .listview").click(function(e){jQuery("#zz-partner-map-wrapper").hide("swing");jQuery("#zz-partner-list-view-buttons").show("swing")});jQuery("#zz-partner-map .mapview, #zz-partner-list-view-buttons .mapview").click(function(e){jQuery("#zz-partner-map-wrapper").show("swing",function(){zz_map.map._onResize()});jQuery("#zz-partner-list-view-buttons").hide("swing")});var zz_selection=new OipaSelection,zz_organisationlist=new OipaList;zz_organisationlist.selection=zz_selection;zz_organisationlist.init();var zz_filter=new OipaFilters;zz_filter.selection=zz_selection;var zz_map=new OipaMap;zz_map.selection=zz_selection;zz_map.init();