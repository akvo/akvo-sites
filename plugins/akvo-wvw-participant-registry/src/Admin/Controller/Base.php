<?php
namespace Akvo\WvW\ParticipantRegistry\Admin\Controller;

/**
 * Description of Base
 *
 * @author Jayawi Perera
 */
class Base {

	public function enqueueBootstrapJs () {

		wp_register_script('twitter_bootstrap_js', AkvoWvwParticipantRegistry_Plugin_Url . '/assets/plugins/bootstrap/js/bootstrap.min.js', array('jquery'));
		wp_enqueue_script('twitter_bootstrap_js');

	}

	public function enqueueKwgTbsJs () {

		wp_register_script('kwgtbs_js', AkvoWvwParticipantRegistry_Plugin_Url . '/assets/js/library/kwg_tbs_interface.js', array('jquery'));
		wp_enqueue_script('kwgtbs_js');

	}

	public function enqueueKwgTimer () {

		wp_register_script('kwgtimer_js', AkvoWvwParticipantRegistry_Plugin_Url . '/assets/js/library/kwg_timer.js');
		wp_enqueue_script('kwgtimer_js');

	}

	public function enqueueAdminJs () {

		wp_register_script(AkvoWvwParticipantRegistry_Plugin_Slug . '-admin-general', AkvoWvwParticipantRegistry_Plugin_Url . '/assets/js/admin/admin.js', array('jquery'));
		wp_enqueue_script(AkvoWvwParticipantRegistry_Plugin_Slug . '-admin-general');

	}

	public function enqueueBootstrapCss () {

		wp_register_style('twitter_bootstrap_css', AkvoWvwParticipantRegistry_Plugin_Url . '/assets/plugins/bootstrap/css/bootstrap.min.css');
		wp_enqueue_style('twitter_bootstrap_css');

	}

	public function enqueueFontAwesomeCss () {

		wp_register_style('font_awesome_css', AkvoWvwParticipantRegistry_Plugin_Url . '/assets/plugins/font-awesome/css/font-awesome.min.css');
		wp_enqueue_style('font_awesome_css');

	}

	public function enqueueAdminCss () {

		wp_register_style(AkvoWvwParticipantRegistry_Plugin_Slug . 'admin_css', AkvoWvwParticipantRegistry_Plugin_Url . '/assets/css/admin.css');
		wp_enqueue_style(AkvoWvwParticipantRegistry_Plugin_Slug . 'admin_css');

	}

}