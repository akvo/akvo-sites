<?php
namespace Akvo\WvW\ParticipantRegistry\Fe\Controller;

use Akvo\WvW\ParticipantRegistry\Fe\Model as Model;
/**
 * Description of Map
 *
 * @author Uthpala Sandirigama
 */
class Map {

	public function initialise () {

		$oMap = new Model\Map();
//		$oMap->enqueueCss();
//		$oMap->enqueueScripts();
		$this->enqueueGoogleMapJs();
		$this->enqueueFrontEndCss();

	}

	public function page () {

		$oMap = new Model\Map();

		$sZoomFactor = $oMap->getZoomFactor();
		$aCenterPoint = $oMap->getCenterPoint();
		$sJsonGeocodes = $oMap->getMarkerPoints();
        
        $oRegistry = new \Akvo\WvW\ParticipantRegistry\Admin\Model\Registry();
        $aOrderBy = array(
			'column' => 'city',
			'direction' => 'ASC',
		);
		$aContent['registry'] = $oRegistry->getRegistryForBatch('2015', $aOrderBy);
		
        //add school table
        $aContent['page-config'] = array(
			'batch' => '2015',
			'order_by_column' => 'city',
			'order_by_direction' => 'ASC', 
		);
		ob_start();
		require AkvoWvwParticipantRegistry_Plugin_Dir . '/src/Fe/View/scripts/map/map.phtml';
		return ob_get_clean();

	}

	public function enqueueGoogleMapJs () {

		$sKeyName = \Akvo\WvW\ParticipantRegistry\Config::OPTION_NAME_GOOGLE_MAPS_API_KEY;
		$mKey = get_option($sKeyName, null);

		if (is_null($mKey)) {
			$mKey = \Akvo\WvW\ParticipantRegistry\Config::OPTION_VALUE_GOOGLE_MAPS_API_KEY;
		}

		$sGoogleMapsApiUrl = 'http://maps.googleapis.com/maps/api/js?key=' . $mKey . '&sensor=false';

		$sGoogleMapApiHandle = AkvoWvwParticipantRegistry_Plugin_Slug . '-google-map-api';
		if (!wp_script_is($sGoogleMapApiHandle, 'registered')) {
			wp_register_script($sGoogleMapApiHandle, $sGoogleMapsApiUrl);
		}
		if (!wp_script_is($sGoogleMapApiHandle, 'enqueued')) {
			wp_enqueue_script($sGoogleMapApiHandle);
		}

		$sDisplayMapHandle = AkvoWvwParticipantRegistry_Plugin_Slug . '-display-map';
		if (!wp_script_is($sDisplayMapHandle, 'registered')) {
			wp_register_script($sDisplayMapHandle, AkvoWvwParticipantRegistry_Plugin_Url . '/assets/js/fe/wvw_display_map.js', array('jquery'));
		}
		if (!wp_script_is($sDisplayMapHandle, 'enqueued')) {
			wp_enqueue_script($sDisplayMapHandle);
		}

	}

	public function enqueueFrontEndCss () {

		$sHandle = AkvoWvwParticipantRegistry_Plugin_Slug . '-front-end-css';
		if (!wp_style_is($sHandle, 'registered')) {
			wp_register_style($sHandle, AkvoWvwParticipantRegistry_Plugin_Url . '/assets/css/fe.css');
		}
		if (!wp_style_is($sHandle, 'enqueued')) {
			wp_enqueue_style($sHandle);
		}

	}

}