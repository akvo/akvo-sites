<?php
namespace Akvo\WvW\ParticipantRegistry\Admin\Model;

use Akvo\WvW\ParticipantRegistry\Common\Model\Dao as AWPRDao;
use Akvo\WvW\ParticipantRegistry\Common\Form\Register as RegistryForm;
/**
 * Description of Registry
 *
 * @author Jayawi Perera
 */
class Registry {

	public function getRegistryForBatch ($sBatch = null, $aOrderBy = null, $aLimit = null) {

		if (is_null($sBatch)) {
			$sBatch = (string)(\Akvo\WvW\ParticipantRegistry\Common\Model\Registry::getPastParticipationYears() + 1);
		}

		if (is_null($aOrderBy)) {
			$aOrderBy = array(
				'column' => 'date_created',
				'direction' => 'DESC',
			);
		}
		
        $oRegistryForm = new RegistryForm();
        $aSupportpoints = $oRegistryForm->getSupportPointOptions();
		$oDaoParticipantRegistry = new AWPRDao\ParticipantRegistry();
		$aBatch = $oDaoParticipantRegistry->fetchByBatch($sBatch, $aOrderBy['column'], $aOrderBy['direction'], $aLimit);
        foreach($aBatch AS $k=>$aRegistry){
            $aBatch[$k]['support_point'] = $aSupportpoints[$aRegistry['support_point']];
            
        }
        return $aBatch;

	}

	public function getBatches () {

		$oDaoParticipantRegistry = new AWPRDao\ParticipantRegistry();
		return $oDaoParticipantRegistry->fetchBatches();

	}

	public function getRegistrant () {

		$aContent = array(
			'redirect' => false,
		);

		$sRedirectUrl = menu_page_url(\Akvo\WvW\ParticipantRegistry\Admin\Controller\Home::MENU_SLUG, false);

		if (!isset($_GET['id'])) {
			$aContent['redirect'] = $sRedirectUrl;
			return $aContent;
		}

		$iId = (int)$_GET['id'];
		$oDaoParticipantRegistry = new AWPRDao\ParticipantRegistry();
		$aRegistrantDetail = $oDaoParticipantRegistry->fetch($iId);
		if (is_null($aRegistrantDetail)) {
			$aContent['redirect'] = $sRedirectUrl;
			return $aContent;
		}

		$aContent['detail'] = $aRegistrantDetail;

		return $aContent;

	}

	public function export ($sBatch) {

		$sPathToFile = null;

		if (is_null($sBatch) || $sBatch == '') {
			$sBatch = (string)(\Akvo\WvW\ParticipantRegistry\Common\Model\Registry::getPastParticipationYears() + 1);
		}

		$aOrderBy = array(
			'column' => 'date_created',
			'direction' => 'DESC',
		);

		$oDaoParticipantRegistry = new AWPRDao\ParticipantRegistry();
		$aListing = $oDaoParticipantRegistry->fetchByBatch($sBatch, $aOrderBy['column'], $aOrderBy['direction']);
//var_dump($aListing);die();
		$aUploadsConfig = wp_upload_dir();

		if (count($aListing) > 0) {

			$sFileName = 'ParticipantRegistryExport_'.date('Ymd').'_' . $sBatch .'.xls';
			$sFilePath = $aUploadsConfig['basedir'] . '/' . $sFileName;
//			$sFilePath = AkvoWvwParticipantRegistry_Plugin_Dir . '/' . $sFileName;
			$oXslxWriter = new \KwgPress\Xlsx($sFilePath);
			$oXslxWriter->create();
			$oXslxWriter->startWorkbook();

				$oXslxWriter->startStyles();

					$oXslxWriter->startStyle('sHeading');
						$aStyleHeadingFontOptions = array(
							'bold' => 1,
							'colour' => '#FFFFFF',
							'size' => 12,
						);
						$oXslxWriter->startFont($aStyleHeadingFontOptions)->endFont();
						$aStyleHeadingInteriorOptions = array(
							'colour' => '#6699FF',
							'pattern' => 'Solid',
						);
						$oXslxWriter->startInterior($aStyleHeadingInteriorOptions)->endInterior();

					$oXslxWriter->endStyle();

				$oXslxWriter->endStyles();

				$oXslxWriter->startWorksheet($sBatch);

					$oXslxWriter->startTable();

						$oXslxWriter->startRow();

							$oXslxWriter->startCell(array('style_id' => 'sHeading'))
								->outputData('ID')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('School Name')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Address')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('City')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Postal Code')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Contact Person Name')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Email')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Phone')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Participation')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Groups: Grade 7')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Groups: Grade 8')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Groups: Grade 6/7')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Groups: Grade 6/7/8/')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Groups: Grade 7/8')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Total Students')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Support Point')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Date of Walk')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('City of Walk')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Comments')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Latitude')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Longitude')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Date Created')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Date Updated')
								->endCell();

						$oXslxWriter->endRow();
        $oRegistryForm = new RegistryForm();
        $aSupportpoints = $oRegistryForm->getSupportPointOptions();
		
			foreach ($aListing as $aDetail) {
                
						$oXslxWriter->startRow();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['id'], array('type' => 'Number'));
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['name']);
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['address']);
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['city']);
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['postal_code']);
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['contact_name']);
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['email']);
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['phone']);
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['participation']);
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['groups_grade_7'], array('type' => 'Number'));
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['groups_grade_8'], array('type' => 'Number'));
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['groups_grade_6_7'], array('type' => 'Number'));
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['groups_grade_6_7_8'], array('type' => 'Number'));
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['groups_grade_7_8'], array('type' => 'Number'));
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['total_students'], array('type' => 'Number'));
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aSupportpoints[$aDetail['support_point']]);
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData(date("Y-m-d", strtotime($aDetail['date_of_walk'])));
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['city_of_walk']);
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['comments']);
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['latitude'], array('type' => 'Number'));
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['longitude'], array('type' => 'Number'));
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['date_created']);
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['date_updated']);
							$oXslxWriter->endCell();

						$oXslxWriter->endRow();
			}

					$oXslxWriter->endTable();

				$oXslxWriter->endWorksheet();

			$oXslxWriter->endWorkbook();

			$oXslxWriter->close();
			$sPathToFile = $aUploadsConfig['baseurl'] . '/' . $sFileName;

		}

		// Return File Name for Download
		return $sPathToFile;

	}

}