<?php
namespace Akvo\WfW\ParticipantRegistry\Fe\Model;

/**
 *
 * @author Uthpala Sandirigama
 */
class Map {

	public function getMarkerPoints (){
		//addresses procees in to geo codes

		global $wpdb;

		// Fetch Schools that have Registered
		$oRegistry = new \Akvo\WfW\ParticipantRegistry\Admin\Model\Registry();
		$aData = $oRegistry->getRegistryForBatch();

		$aReturn = array();

		foreach ($aData as $aRecord ){
			$aTemp = array(
			'name'=> $aRecord['name'],
			'lat'=> $aRecord['latitude'],
			'long'=> $aRecord['longitude']
			);

			array_push($aReturn, $aTemp);

		}

		$sReturnJson = json_encode($aReturn);
		return $sReturnJson;

	}

	public function getZoomFactor () {
		$sOptionName = \Akvo\WfW\ParticipantRegistry\Config::OPTION_NAME_GOOGLE_MAPS_DEFAULT_ZOOM_FACTOR;
		$mZoomFactor = get_option($sOptionName);

		if($mZoomFactor == FALSE){
			//option not yet saved
			return \Akvo\WfW\ParticipantRegistry\Config::OPTION_VALUE_GOOGLE_MAPS_DEFAULT_ZOOM_FACTOR;
		}else{
			return $mZoomFactor;
		}
	}

	public function getCenterPoint (){
		$sOptionName = \Akvo\WfW\ParticipantRegistry\Config::OPTION_NAME_GOOGLE_MAPS_DEFAULT_CENTER_POINT;
		$mCenter = get_option($sOptionName);
		if($mCenter == FALSE){
			//option not saved
			$sCenterPointX = \Akvo\WfW\ParticipantRegistry\Config::OPTION_VALUE_GOOGLE_MAPS_DEFAULT_CENTER_POINT_LATITUDE;
			$sCenterPointY = \Akvo\WfW\ParticipantRegistry\Config::OPTION_VALUE_GOOGLE_MAPS_DEFAULT_CENTER_POINT_LONGITUDE;
			$aCenterpoint = array(
				'latitude' => $sCenterPointX,
				'longitude' => $sCenterPointY
			);
			return $aCenterpoint;
		}else{
			//$aCenterpoint = implode(',', $mCenter);
			return $mCenter;
		}


	}

}