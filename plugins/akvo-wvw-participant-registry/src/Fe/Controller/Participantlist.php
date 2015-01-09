<?php
namespace Akvo\WvW\ParticipantRegistry\Fe\Controller;

use Akvo\WvW\ParticipantRegistry\Fe\Model as Model;
/**
 * Description of Map
 *
 * @author Uthpala Sandirigama
 */
class Participantlist {

	public function initialise () {

		//$oMap = new Model\Map();

		$this->enqueueFrontEndCss();

	}

	public function page () {

		
        
        $sBatch = (string)(\Akvo\WvW\ParticipantRegistry\Common\Model\Registry::getPastParticipationYears() + 1);
		if (isset($_GET['batch'])) {
			$sBatch = $_GET['batch'];
		}

		$sOrderByColumn = 'date_created';
		if (isset($_GET['order_by_column'])) {
			$sOrderByColumn = $_GET['order_by_column'];
		}
		$sOrderByDirection = 'DESC';
		if (isset($_GET['order_by_direction'])) {
			$sOrderByDirection = $_GET['order_by_direction'];
		}
		$aOrderBy = array(
			'column' => $sOrderByColumn,
			'direction' => $sOrderByDirection,
		);
        $iPage = 1;
		if (isset($_GET['paging'])) {
			$iPage = (int)$_GET['paging'];
		}
		// Fetch Schools that have Registered
        $iViewAmount = 50;
		$oRegistry = new \Akvo\WvW\ParticipantRegistry\Admin\Model\Registry();
        $aRegistry = $oRegistry->getRegistryForBatch($sBatch, $aOrderBy);
        $iTotal = count($aRegistry);
        $iPages = ceil($iTotal/$iViewAmount);
        $aRegistry = array_slice($aRegistry, ($iPage-1)*$iViewAmount, $iPage*$iViewAmount);
		$aContent['total'] = $iTotal;
		$aContent['page'] = $iPage;
		$aContent['pages'] = $iPages;
		$aContent['registry'] = $aRegistry;
		$aContent['batches'] = $oRegistry->getBatches();
		$aContent['page-config'] = array(
			'batch' => $sBatch,
			'order_by_column' => $sOrderByColumn,
			'order_by_direction' => $sOrderByDirection,
		);
		ob_start();
		require AkvoWvwParticipantRegistry_Plugin_Dir . '/src/Fe/View/scripts/participantlist/participantlist.phtml';
		return ob_get_clean();

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