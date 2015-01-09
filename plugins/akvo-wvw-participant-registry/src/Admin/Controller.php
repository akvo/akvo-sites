<?php
namespace Akvo\WvW\ParticipantRegistry\Admin;
/**
 * Description of Controller
 *
 * @author Jayawi Perera
 */
class Controller {

	public function initialise () {

		$oHomeController = new Controller\Home();
		add_action('admin_menu', array($oHomeController, 'initialise'));

		$oSettingsController = new Controller\Settings();
		add_action('admin_menu', array($oSettingsController, 'initialise'));

		$oRegistrantDetailController = new Controller\Registrant\Detail();
		add_action('admin_menu', array($oRegistrantDetailController, 'initialise'));

		$oRegistrantEditController = new Controller\Registrant\Edit();
		add_action('admin_menu', array($oRegistrantEditController, 'initialise'));

		$oRegistrantRemoveController = new Controller\Registrant\Remove();
		add_action('admin_menu', array($oRegistrantRemoveController, 'initialise'));
		
        $oSupportPointController = new Controller\Supportpoint();
		add_action('admin_menu', array($oSupportPointController, 'initialise'));

		$this->_initialiseAjax();

	}

	public function initialiseLimited () {

		$oLimitedHomeController = new Controller\LimitedHome();
		add_action('admin_menu', array($oLimitedHomeController, 'initialise'));

	}

	private function _initialiseAjax () {

		$oAjaxController = new Controller\Ajax();
		add_action('wp_ajax_' . AkvoWvwParticipantRegistry_Plugin_Slug . '_export', array($oAjaxController, 'export'));

	}

}