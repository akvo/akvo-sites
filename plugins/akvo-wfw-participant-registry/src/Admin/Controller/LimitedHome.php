<?php
namespace Akvo\WfW\ParticipantRegistry\Admin\Controller;

/**
 * Description of Home
 *
 * @author Jayawi Perera
 */
class LimitedHome extends Base {

	public function initialise () {

		$sHookName = add_menu_page(
				'Wandelen voor Water - Participant Registry',
				'Participant Registry',
				\Akvo\WfW\ParticipantRegistry\Config::CAPABILITY_GENERAL_NAME,
				'AWPR_limited_home',
				array($this, 'page'),
				null,
				null
			);

//		add_action('admin_print_styles', array($this, 'enqueueAdminIconCss'));

		add_action('admin_print_scripts-' . $sHookName, array($this, 'enqueueBootstrapJs'));
		add_action('admin_print_styles-' . $sHookName, array($this, 'enqueueBootstrapCss'));
		add_action('admin_print_styles-' . $sHookName, array($this, 'enqueueFontAwesomeCss'));
		add_action('admin_print_styles-' . $sHookName, array($this, 'enqueueAdminCss'));

	}

	public function page () {

		include_once AkvoWfwParticipantRegistry_Plugin_Dir . '/src/Admin/View/scripts/home/limited_home.phtml';

	}

}