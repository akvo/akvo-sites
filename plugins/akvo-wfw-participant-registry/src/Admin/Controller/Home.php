<?php
namespace Akvo\WfW\ParticipantRegistry\Admin\Controller;

/**
 * Description of Home
 *
 * @author Jayawi Perera
 */
class Home extends Base {

	const MENU_SLUG = 'AWPR_home';

	public function initialise () {

		$sHookName = add_menu_page(
				'Wandelen voor Water - Participant Registry',
				'Participant Registry',
				\Akvo\WfW\ParticipantRegistry\Config::CAPABILITY_GENERAL_NAME,
				self::MENU_SLUG,
				array($this, 'page'),
				AkvoWfwParticipantRegistry_Plugin_Url . '/assets/img/akvo_wfw_pr_icon.png',
				null
			);

		//		add_action('admin_print_styles', array($this, 'enqueueAdminIconCss'));

		add_action('admin_print_scripts-' . $sHookName, array($this, 'enqueueBootstrapJs'));
		add_action('admin_print_scripts-' . $sHookName, array($this, 'enqueueKwgTbsJs'));
		add_action('admin_print_scripts-' . $sHookName, array($this, 'enqueueHomeJs'));

		add_action('admin_print_styles-' . $sHookName, array($this, 'enqueueBootstrapCss'));
		add_action('admin_print_styles-' . $sHookName, array($this, 'enqueueFontAwesomeCss'));
		add_action('admin_print_styles-' . $sHookName, array($this, 'enqueueAdminCss'));

	}

	public function page () {

		$sBatch = (string)(\Akvo\WfW\ParticipantRegistry\Common\Model\Registry::getPastParticipationYears() + 1);
		if (isset($_GET['batch'])) {
			$sBatch = $_GET['batch'];
		}

		$sOrderByColumn = 'country';
		if (isset($_GET['order_by_column'])) {
			$sOrderByColumn = $_GET['order_by_column'];
		}
		$sOrderByDirection = 'ASC';
		if (isset($_GET['order_by_direction'])) {
			$sOrderByDirection = $_GET['order_by_direction'];
		}
		$aOrderBy = array(
			'column' => $sOrderByColumn,
			'direction' => $sOrderByDirection,
		);

		// Fetch Schools that have Registered
		$oRegistry = new \Akvo\WfW\ParticipantRegistry\Admin\Model\Registry();
		$aContent['registry'] = $oRegistry->getRegistryForBatch($sBatch, $aOrderBy);
		$aContent['batches'] = $oRegistry->getBatches();
		$aContent['page-config'] = array(
			'page' => self::MENU_SLUG,
			'batch' => $sBatch,
			'order_by_column' => $sOrderByColumn,
			'order_by_direction' => $sOrderByDirection,
		);

		include_once AkvoWfwParticipantRegistry_Plugin_Dir . '/src/Admin/View/scripts/home/home.phtml';

	}

	public function enqueueHomeJs () {

		wp_register_script(AkvoWfwParticipantRegistry_Plugin_Slug . '-admin-home', AkvoWfwParticipantRegistry_Plugin_Url . '/assets/js/admin/home.js');
		wp_enqueue_script(AkvoWfwParticipantRegistry_Plugin_Slug . '-admin-home');

	}

}