<?php
namespace Akvo\WfW\ParticipantRegistry\Admin\Controller\Registrant;

use Akvo\WfW\ParticipantRegistry\Admin\Controller\Base as AWPRAdminControllerBase;
/**
 * Description of Registrant
 *
 * @author Jayawi Perera
 */
class Edit extends AWPRAdminControllerBase {

	const MENU_SLUG = 'AWPR_registrant_edit';

	public function initialise () {

		$sHookName = add_submenu_page(
				null,
				'Wandelen voor Water - Participant Registry: Settings',
				'Settings',
				\Akvo\WfW\ParticipantRegistry\Config::CAPABILITY_GENERAL_NAME,
				self::MENU_SLUG,
				array($this, 'page')
			);

		//		add_action('admin_print_styles', array($this, 'enqueueAdminIconCss'));

		add_action('admin_print_scripts-' . $sHookName, array($this, 'enqueueBootstrapJs'));
		add_action('admin_print_scripts-' . $sHookName, array($this, 'enqueueBootstrapDatePickerJs'));
		add_action('admin_print_scripts-' . $sHookName, array($this, 'enqueueAdminJs'));

		add_action('admin_print_styles-' . $sHookName, array($this, 'enqueueBootstrapCss'));
		add_action('admin_print_styles-' . $sHookName, array($this, 'enqueueFontAwesomeCss'));
		add_action('admin_print_styles-' . $sHookName, array($this, 'enqueueBootstrapDatePickerCss'));
		add_action('admin_print_styles-' . $sHookName, array($this, 'enqueueAdminCss'));

	}

	public function page () {

		$oFormHandler = new \Akvo\WfW\ParticipantRegistry\Admin\Model\FormHandler();
		$aContent = $oFormHandler->edit();

//		$oRegistry = new \Akvo\WfW\ParticipantRegistry\Admin\Model\Registry();
//		$aContent = $oRegistry->getRegistrant();

		include_once AkvoWfwParticipantRegistry_Plugin_Dir . '/src/Admin/View/scripts/registrant/edit.phtml';

	}

	public function enqueueBootstrapDatePickerJs () {

		wp_register_script('twitter_bootstrap_date_picker_js', AkvoWfwParticipantRegistry_Plugin_Url . '/assets/plugins/bootstrap_datepicker/js/bootstrap-datepicker.js');
		wp_enqueue_script('twitter_bootstrap_date_picker_js');

		wp_register_script('twitter_bootstrap_date_picker_en_js', AkvoWfwParticipantRegistry_Plugin_Url . '/assets/plugins/bootstrap_datepicker/js/locales/bootstrap-datepicker.nl.js');
		wp_enqueue_script('twitter_bootstrap_date_picker_en_js');

	}

	public function enqueueBootstrapDatePickerCss () {

		wp_register_style('twitter_bootstrap_date_picker_css', AkvoWfwParticipantRegistry_Plugin_Url . '/assets/plugins/bootstrap_datepicker/css/datepicker.css');
		wp_enqueue_style('twitter_bootstrap_date_picker_css');

	}


}