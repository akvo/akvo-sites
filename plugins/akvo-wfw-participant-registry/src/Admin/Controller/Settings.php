<?php
namespace Akvo\WfW\ParticipantRegistry\Admin\Controller;
/**
 * Description of Settings
 *
 * @author Jayawi Perera
 */
class Settings extends Base {

	const MENU_SLUG = 'AWPR_settings';

	public function initialise () {

		$sHookName = add_submenu_page(
				Home::MENU_SLUG,
				'Wandelen voor Water - Participant Registry: Settings',
				'Settings',
				\Akvo\WfW\ParticipantRegistry\Config::CAPABILITY_GENERAL_NAME,
				self::MENU_SLUG,
				array($this, 'page')
			);

		//		add_action('admin_print_styles', array($this, 'enqueueAdminIconCss'));

		add_action('admin_print_scripts-' . $sHookName, array($this, 'enqueueBootstrapJs'));
		add_action('admin_print_scripts-' . $sHookName, array($this, 'enqueueAdminJs'));

		add_action('admin_print_styles-' . $sHookName, array($this, 'enqueueBootstrapCss'));
		add_action('admin_print_styles-' . $sHookName, array($this, 'enqueueFontAwesomeCss'));
		add_action('admin_print_styles-' . $sHookName, array($this, 'enqueueAdminCss'));

	}

	public function page () {

		$oSettings = new \Akvo\WfW\ParticipantRegistry\Admin\Model\Settings();
		$aContent = $oSettings->manage();

		include_once AkvoWfwParticipantRegistry_Plugin_Dir . '/src/Admin/View/scripts/settings/settings.phtml';

	}

}