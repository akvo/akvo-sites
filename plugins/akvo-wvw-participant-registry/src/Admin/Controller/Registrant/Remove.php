<?php
namespace Akvo\WvW\ParticipantRegistry\Admin\Controller\Registrant;

use Akvo\WvW\ParticipantRegistry\Admin\Controller\Base as AWPRAdminControllerBase;
/**
 * Description of Registrant
 *
 * @author Jayawi Perera
 */
class Remove extends AWPRAdminControllerBase {

	const MENU_SLUG = 'AWPR_registrant_remove';

	public function initialise () {

		$sHookName = add_submenu_page(
				null,
				'Wandelen voor Water - Participant Registry: Settings',
				'Settings',
				\Akvo\WvW\ParticipantRegistry\Config::CAPABILITY_GENERAL_NAME,
				self::MENU_SLUG,
				array($this, 'page')
			);

		//		add_action('admin_print_styles', array($this, 'enqueueAdminIconCss'));

		add_action('admin_print_scripts-' . $sHookName, array($this, 'enqueueBootstrapJs'));
		add_action('admin_print_scripts-' . $sHookName, array($this, 'enqueueKwgTimer'));
		add_action('admin_print_scripts-' . $sHookName, array($this, 'enqueueAdminJs'));

		add_action('admin_print_styles-' . $sHookName, array($this, 'enqueueBootstrapCss'));
		add_action('admin_print_styles-' . $sHookName, array($this, 'enqueueFontAwesomeCss'));
		add_action('admin_print_styles-' . $sHookName, array($this, 'enqueueAdminCss'));

	}

	public function page () {

		$oFormHandler = new \Akvo\WvW\ParticipantRegistry\Admin\Model\FormHandler();
		$aContent = $oFormHandler->remove();

//		$oRegistry = new \Akvo\WvW\ParticipantRegistry\Admin\Model\Registry();
//		$aContent = $oRegistry->getRegistrant();

		include_once AkvoWvwParticipantRegistry_Plugin_Dir . '/src/Admin/View/scripts/registrant/remove.phtml';

	}

}