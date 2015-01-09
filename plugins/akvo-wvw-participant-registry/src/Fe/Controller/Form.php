<?php
namespace Akvo\WvW\ParticipantRegistry\Fe\Controller;

use KwgPress as KwgP;

/**
 * Description of Form
 *
 * @author Jayawi Perera
 */
class Form {

	public function initialise () {

		$this->enqueueFrontEndJs();
		$this->enqueueFrontEndCss();

	}

	public function page () {

		$oFormHandler = new \Akvo\WvW\ParticipantRegistry\Fe\Model\FormHandler();
		$aContent = $oFormHandler->process();

		ob_start();
		require AkvoWvwParticipantRegistry_Plugin_Dir . '/src/Fe/View/scripts/form/form.phtml';
		return ob_get_clean();

	}

	public function enqueueFrontEndJs () {

		$sDisplayFormHandle = AkvoWvwParticipantRegistry_Plugin_Slug . '-display-form';
		if (!wp_script_is($sDisplayFormHandle, 'registered')) {
			wp_register_script($sDisplayFormHandle, AkvoWvwParticipantRegistry_Plugin_Url . '/assets/js/fe/wvw_display_form.js', array('jquery'));
		}
		if (!wp_script_is($sDisplayFormHandle, 'enqueued')) {
			wp_enqueue_script($sDisplayFormHandle);
		}

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