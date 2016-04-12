<?php
namespace Akvo\WfW\ParticipantRegistry\Admin\Model;

use Akvo\WfW\ParticipantRegistry\Common\Model\Dao as AWPRDao;
use Akvo\WfW\ParticipantRegistry\Common\Form\Register as RegistryForm;
/**
 * Description of Registry
 *
 * @author Jayawi Perera
 */
class Registry {

	public function getRegistryForBatch ($sBatch = null, $aOrderBy = null, $aLimit = null) {

		if (is_null($sBatch)) {
			$sBatch = (string)(\Akvo\WfW\ParticipantRegistry\Common\Model\Registry::getPastParticipationYears() + 1);
		}

		if (is_null($aOrderBy)) {
			$aOrderBy = array(
				'column' => 'date_created',
				'direction' => 'DESC',
			);
		}
		
        $oRegistryForm = new RegistryForm();
		$oDaoParticipantRegistry = new AWPRDao\ParticipantRegistry();
		$aBatch = $oDaoParticipantRegistry->fetchByBatch($sBatch, $aOrderBy['column'], $aOrderBy['direction'], $aLimit);
        
        return $aBatch;

	}

	public function getBatches () {

		$oDaoParticipantRegistry = new AWPRDao\ParticipantRegistry();
		return $oDaoParticipantRegistry->fetchBatches();

	}

	public function getRegistrant () {
        global $wpdb;
		$aContent = array(
			'redirect' => false,
		);

		$sRedirectUrl = menu_page_url(\Akvo\WfW\ParticipantRegistry\Admin\Controller\Home::MENU_SLUG, false);

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
        $aRegistrantDetail['project'] = $wpdb->get_var( "SELECT title FROM ".$wpdb->prefix."projects WHERE project_id=".$aRegistrantDetail['id_project'] );
		$aContent['detail'] = $aRegistrantDetail;

		return $aContent;

	}

	public function export ($sBatch) {
        global $wpdb;
		$sPathToFile = null;

		if (is_null($sBatch) || $sBatch == '') {
			$sBatch = (string)(\Akvo\WfW\ParticipantRegistry\Common\Model\Registry::getPastParticipationYears() + 1);
		}

		$aOrderBy = array(
			'column' => 'date_created',
			'direction' => 'DESC',
		);

		$oDaoParticipantRegistry = new AWPRDao\ParticipantRegistry();
		$aListing = $oDaoParticipantRegistry->fetchByBatch($sBatch, $aOrderBy['column'], $aOrderBy['direction']);

		$aUploadsConfig = wp_upload_dir();

		if (count($aListing) > 0) {

			$sFileName = 'ParticipantRegistryExport_'.date('Ymd').'_' . $sBatch . '.xls';
			$sFilePath = $aUploadsConfig['basedir'] . '/' . $sFileName;
//			$sFilePath = AkvoWfwParticipantRegistry_Plugin_Dir . '/' . $sFileName;
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
								->outputData('Support center organisation')
								->endCell();
                            
                            $oXslxWriter->startCell()
								->outputData('Contact Person Name')
								->endCell();
                            
							$oXslxWriter->startCell()
								->outputData('Country')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Email')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Address line 1')
								->endCell();
                            
							$oXslxWriter->startCell()
								->outputData('Address line 2')
								->endCell();
                            
							$oXslxWriter->startCell()
								->outputData('Address line 3')
								->endCell();
                            
                            $oXslxWriter->startCell()
								->outputData('Date of Walk')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Total Schools')
								->endCell();
                            
							$oXslxWriter->startCell()
								->outputData('Total Students')
								->endCell();

							$oXslxWriter->startCell()
								->outputData('Project')
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
        
		
			foreach ($aListing as $aDetail) {
                        $aDetail['project'] = $wpdb->get_var( "SELECT title FROM ".$wpdb->prefix."projects WHERE project_id=".$aDetail['id_project'] );
		
						$oXslxWriter->startRow();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['id'], array('type' => 'Number'));
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['name']);
							$oXslxWriter->endCell();
                            
                            $oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['support_point']);
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['contact_name']);
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['country']);
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['email']);
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['address1']);
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['address2']);
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['address3']);
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData(date("Y-m-d", strtotime($aDetail['date_of_walk'])));
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['total_schools'], array('type' => 'Number'));
							$oXslxWriter->endCell();
                            
							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['total_students'], array('type' => 'Number'));
							$oXslxWriter->endCell();

							$oXslxWriter->startCell();
							$oXslxWriter->outputData($aDetail['project']);
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