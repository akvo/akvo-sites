<?php
namespace Akvo\WfW\ParticipantRegistry\Admin\Form;

use KwgPress as KwgP;
/**
 * Description of Settings
 *
 * @author Jayawi Perera
 */
class Settings extends KwgP\Form {

	protected $_sPluginSlug = AkvoWfwParticipantRegistry_Plugin_Slug;

	public function init() {

		$oGoogleMapsApiKey = new \Zend_Form_Element_Text('textGoogleMapsApiKey');
		$oGoogleMapsApiKey->setLabel('API Key');

		$oGoogleMapsDefaultZoomFactor = new \Zend_Form_Element_Text('textGoogleMapsDefaultZoomFactor');
		$oGoogleMapsDefaultZoomFactor->setLabel('Default Zoom Factor');

		$oGoogleMapsDefaultCenterPointLatitude = new \Zend_Form_Element_Text('textGoogleMapsDefaultCenterPointLatitude');
		$oGoogleMapsDefaultCenterPointLatitude->setLabel('Default Center Point - Latitude');

		$oGoogleMapsDefaultCenterPointLongitude = new \Zend_Form_Element_Text('textGoogleMapsDefaultCenterPointLongitude');
		$oGoogleMapsDefaultCenterPointLongitude->setLabel('Default Center Point - Longitude');
        
        $oSubmit = new \Zend_Form_Element_Button('submitSubmit');
		$oSubmit->setLabel('Submit')
				->setAttrib('type', 'submit')
				->setDecorators(array('ViewHelper'));

		switch ($this->_sContext) {

			case self::CONTEXT_CREATE:

				$oGoogleMapsApiKey->setRequired(true);
				$oGoogleMapsApiKey->setAttrib('class', 'input-xlarge');

				$oGoogleMapsDefaultZoomFactor->setRequired(true);
				$oGoogleMapsDefaultZoomFactor->setAttrib('class', 'input-xlarge');
				$oGoogleMapsDefaultZoomFactor->addValidator('Int', true);
				$oGoogleMapsDefaultZoomFactor->addValidator('Between', true, array('min' => 0, 'max' => 19));

				$oGoogleMapsDefaultCenterPointLatitude->setRequired(true);
				$oGoogleMapsDefaultCenterPointLatitude->setAttrib('class', 'input-xlarge');
				
				$oGoogleMapsDefaultCenterPointLongitude->setRequired(true);
				$oGoogleMapsDefaultCenterPointLongitude->setAttrib('class', 'input-xlarge');
				
				$oSubmit->setLabel('<i class="icon icon-save"></i>&nbsp; Save')
						->setAttrib('escape', false)
						->setAttrib('class', 'btn btn-success');

				$aCreateElements = array(
					$oGoogleMapsApiKey,
					$oGoogleMapsDefaultZoomFactor,
					$oGoogleMapsDefaultCenterPointLatitude,
					$oGoogleMapsDefaultCenterPointLongitude,
                    $oSubmit,
				);
				$this->addElements($aCreateElements);

				break;

		}

		$this->_sViewScript = '/forms/settings.phtml';
//		$this->_sViewScript = '/forms/generic_two_column.phtml';

		$this->setDecorators(array(array('ViewScript', array('viewScript' => $this->_sViewScript))));

	}

}