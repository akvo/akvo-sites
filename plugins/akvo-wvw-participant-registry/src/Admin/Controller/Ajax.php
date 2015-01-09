<?php
namespace Akvo\WvW\ParticipantRegistry\Admin\Controller;

/**
 * Description of Ajax
 *
 * @author Jayawi Perera
 */
class Ajax {

	public static function export () {

		$aResponse = array();

		if (isset($_POST)) {

			if (isset($_POST['batch'])) {

				$sBatch = $_POST['batch'];

				$oRegistry = new \Akvo\WvW\ParticipantRegistry\Admin\Model\Registry();
				$sPathToFile = $oRegistry->export($sBatch);

				$aResponse['status'] = 'successful';
				$aResponse['message'] = 'Download List request successful. Click the button below to download.';
				$aResponse['link'] = $sPathToFile;


			} else {
				$aResponse['status'] = 'failed';
				$aResponse['message'] = 'Download List request failed. Required data has not been provided.';
			}

		} else {
			$aResponse['status'] = 'failed';
			$aResponse['message'] = 'Download List request failed. The request type is invalid.';
		}

		echo json_encode($aResponse);
		die();

	}

}