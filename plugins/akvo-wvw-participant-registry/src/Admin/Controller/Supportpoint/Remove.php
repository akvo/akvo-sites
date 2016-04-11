<?php
namespace Akvo\WvW\ParticipantRegistry\Admin\Controller\Supportpoint;

use Akvo\WvW\ParticipantRegistry\Admin\Controller\Base as AWPRAdminControllerBase;
/**
 * Description of Registrant
 *
 * @author Rumeshkumar <rumeshin@gmail.com>
 */
class Remove extends AWPRAdminControllerBase {
	
	const MENU_SLUG = 'AWPR_supportpoint_remove';

	public function initialise () {

		$sHookName = add_submenu_page(
				null,
				'Wandelen voor Water - Participant Registry: Support points',
				'Supportpoint',
				\Akvo\WvW\ParticipantRegistry\Config::CAPABILITY_GENERAL_NAME,
				self::MENU_SLUG,
				array($this, 'page')
			);		

		add_action('admin_print_scripts-' . $sHookName, array($this, 'enqueueBootstrapJs'));
		add_action('admin_print_scripts-' . $sHookName, array($this, 'enqueueKwgTimer'));
		add_action('admin_print_scripts-' . $sHookName, array($this, 'enqueueAdminJs'));

		add_action('admin_print_styles-' . $sHookName, array($this, 'enqueueBootstrapCss'));
		add_action('admin_print_styles-' . $sHookName, array($this, 'enqueueFontAwesomeCss'));
		add_action('admin_print_styles-' . $sHookName, array($this, 'enqueueAdminCss'));

	}

	public function page () {

		$oModel = new \Akvo\WvW\ParticipantRegistry\Admin\Model\Supportpoint();
		$aContent = $oModel->remove();

		include_once AkvoWvwParticipantRegistry_Plugin_Dir . '/src/Admin/View/scripts/supportpoint/remove.phtml';

	}
}