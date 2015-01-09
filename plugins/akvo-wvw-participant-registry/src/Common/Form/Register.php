<?php
namespace Akvo\WvW\ParticipantRegistry\Common\Form;

use KwgPress as KwgP;
use Akvo\WvW\ParticipantRegistry\Common\Model\Dao\ParticipantRegistry as DaoRegistry;
/**
 * Description of Definition
 *
 * @author Jayawi Perera
 */
class Register extends KwgP\Form {

	protected $_sPluginSlug = AkvoWvwParticipantRegistry_Plugin_Slug;

	public function init() {

		$oId = new \Zend_Form_Element_Hidden('hiddenId');

		$oName = new \Zend_Form_Element_Text('textName');
		$oName->setLabel('Naam van de school');

		$oAddress = new \Zend_Form_Element_Text('textAddress');
		$oAddress->setLabel('Adres');

		$oCity = new \Zend_Form_Element_Text('textCity');
		$oCity->setLabel('Plaats');

		$oPostalCode = new \Zend_Form_Element_Text('textPostalCode');
		$oPostalCode->setLabel('Postcode');

		$oContactName = new \Zend_Form_Element_Text('textContactName');
		$oContactName->setLabel('Naam contactpersoon');

		$oEmail = new \Zend_Form_Element_Text('textEmail');
		$oEmail->setLabel('Emailadres');

		$oPhone = new \Zend_Form_Element_Text('textPhone');
		$oPhone->setLabel('Telefoonnummer');


		$oParticipatedBefore = new \Zend_Form_Element_Radio('radioParticipatedBefore');
		$oParticipatedBefore->setLabel('Heeft de school in de afgelopen jaren eerder meegedaan met Wandelen voor Water? U kunt meerdere opties aanklikken.');
		$oParticipatedBefore->setMultiOptions(array(
			'yes' => 'Ja',
			'no' => 'Nee',
		));

		$iLastYear = \Akvo\WvW\ParticipantRegistry\Common\Model\Registry::getPastParticipationYears();

		$oParticipatedLastYear = new \Zend_Form_Element_Checkbox('checkboxParticipatedLastYear');
		$oParticipatedLastYear->setLabel($iLastYear);

		$oParticipatedYearBeforeLast = new \Zend_Form_Element_Checkbox('checkboxParticipatedYearBeforeLast');
		$oParticipatedYearBeforeLast->setLabel($iLastYear - 1);

		$oParticipatedBeforeTheLast2Years = new \Zend_Form_Element_Checkbox('checkboxParticipatedBeforeTheLastTwoYears');
		$oParticipatedBeforeTheLast2Years->setLabel('meer dan twee jaar geleden');




		$oGrade7Groups = new \Zend_Form_Element_Text('textGrade7Groups');
		$oGrade7Groups->setLabel('Aantal groepen 7');

		$oGrade8Groups = new \Zend_Form_Element_Text('textGrade8Groups');
		$oGrade8Groups->setLabel('Aantal groepen 8');

		$oGrade67Groups = new \Zend_Form_Element_Text('textGrade67Groups');
		$oGrade67Groups->setLabel('Aantal groepen 6/7');

		$oGrade678Groups = new \Zend_Form_Element_Text('textGrade678Groups');
		$oGrade678Groups->setLabel('Aantal groepen 6/7/8');

		$oGrade78Groups = new \Zend_Form_Element_Text('textGrade78Groups');
		$oGrade78Groups->setLabel('Aantal groepen 7/8');

		$oTotalStudents = new \Zend_Form_Element_Text('textTotalStudents');
		$oTotalStudents->setLabel('Totaal aantal leerlingen');


		$oSupportPoint = new \Zend_Form_Element_Select('selectSupportPoint');
		$oSupportPoint->setLabel('Met welk steunpunt werkt de school samen? Als het steunpunt nog niet bekend is, kies dan "(nog onbekend)" uit de lijst');
		$oSupportPoint->setMultiOptions($this->getSupportPointOptions());

		$oWalkDate = new  \Zend_Form_Element_Text('textWalkDate');
		$oWalkDate->setLabel('Datum wandeling');

		$oWalkCity = new  \Zend_Form_Element_Text('textWalkCity');
		$oWalkCity->setLabel('Plaats wandeling');

		$oAgree = new \Zend_Form_Element_Checkbox('checkboxAgree');
		$oAgree->setLabel('Ja, ik ga akkoord met de voorwaarden (zie boven)');

		$oComments = new \Zend_Form_Element_Textarea('textComments');
		$oComments->setLabel('Opmerkingen');

		$oBatch = new \Zend_Form_Element_Text('textBatch');
		$oBatch->setLabel('Batch');

		$oLatitude = new \Zend_Form_Element_Text('textLatitude');
		$oLatitude->setLabel('Latitude');

		$oLongitude = new \Zend_Form_Element_Text('textLongitude');
		$oLongitude->setLabel('Longitude');

		$oSubmit = new \Zend_Form_Element_Button('submitSubmit');
		$oSubmit->setLabel('Submit')
				->setAttrib('type', 'submit')
				->setDecorators(array('ViewHelper'));

		switch ($this->_sContext) {

			case self::CONTEXT_CREATE:

				$oName->setRequired(true);

				$oAddress->setRequired(true);

				$oCity->setRequired(true);

				$oPostalCode->setRequired(true);


				$oContactName->setRequired(true);

				$oEmail->setRequired(true);
				$oEmail->addValidator('EmailAddress');

				$oGrade7Groups->addValidator('Int');
				$oGrade8Groups->addValidator('Int');
				$oGrade67Groups->addValidator('Int');
				$oGrade678Groups->addValidator('Int');
				$oGrade78Groups->addValidator('Int');

				$oTotalStudents->setRequired(true);
				$oTotalStudents->addValidator('Int');

				$oSupportPoint->setRequired(true);

				$oWalkDate->addValidator(
						'Date',
						true,
						array(
							'format' => 'dd-MM-YYYY',
							'messages' => array(
								\Zend_Validate_Date::INVALID => "'%value%' lijkt geen geldige datum te zijn",
								\Zend_Validate_Date::INVALID_DATE => "'%value%' lijkt geen geldige datum te zijn",
								\Zend_Validate_Date::FALSEFORMAT => "'%value%' lijkt geen geldige datum te zijn",
							),

						)
					);
                $oWalkDateMinValidator = new \Zend_Validate_Callback(function($value){
                    if(strtotime($value) <= strtotime(date('Y-m-d'))){
                        return false;
                    }else{
                        return true;
                    }
                });
				$oWalkDate->addValidator($oWalkDateMinValidator);

				$oAgree->setRequired(true);
				$oAgree->addValidator(
						'InArray',
						true,
						array(
							'haystack' => array(1),
							'messages' => array(
							\Zend_Validate_InArray::NOT_IN_ARRAY => "U dient akkoord te zijn met de algemene voorwaarden",
							),
						)
					);

				$oSubmit->setLabel('Submit');

				$aCreateElements = array(
					$oName,
					$oAddress,
					$oCity,
					$oPostalCode,
					$oContactName,
					$oEmail,
					$oPhone,

					$oParticipatedBefore,
					$oParticipatedLastYear,
					$oParticipatedYearBeforeLast,
					$oParticipatedBeforeTheLast2Years,

					$oGrade7Groups,
					$oGrade8Groups,
					$oGrade67Groups,
					$oGrade678Groups,
					$oGrade78Groups,
					$oTotalStudents,

					$oSupportPoint,
					$oWalkDate,
					$oWalkCity,

					$oAgree,
					$oComments,

					$oSubmit,
				);
				$this->addElements($aCreateElements);

				$this->_sViewScript = '/forms/register.phtml';

				break;

			case self::CONTEXT_UPDATE:

				$oName->setAttrib('class', 'input-xxlarge');
				$oName->setRequired(true);
				$oAddress->setAttrib('class', 'input-xxlarge');
				$oAddress->setRequired(true);
				$oCity->setAttrib('class', 'input-xlarge');
				$oCity->setRequired(true);
				$oPostalCode->setAttrib('class', 'input-xlarge');
				$oPostalCode->setRequired(true);


				$oContactName->setAttrib('class', 'input-xxlarge');
				$oContactName->setRequired(true);
				$oEmail->setAttrib('class', 'input-xlarge');
				$oEmail->setRequired(true);
				$oEmail->addValidator('EmailAddress');
				$oPhone->setAttrib('class', 'input-xlarge');

				$oParticipatedBefore->setSeparator(' ');

				$oGrade7Groups->setAttrib('class', 'input-medium');
				$oGrade7Groups->addValidator('Int');
				$oGrade8Groups->setAttrib('class', 'input-medium');
				$oGrade8Groups->addValidator('Int');
				$oGrade67Groups->setAttrib('class', 'input-medium');
				$oGrade67Groups->addValidator('Int');
				$oGrade678Groups->setAttrib('class', 'input-medium');
				$oGrade678Groups->addValidator('Int');
				$oGrade78Groups->setAttrib('class', 'input-medium');
				$oGrade78Groups->addValidator('Int');

				$oTotalStudents->setAttrib('class', 'input-medium');
				$oTotalStudents->setRequired(true);
				$oTotalStudents->addValidator('Int');

				$oSupportPoint->setAttrib('class', 'input-xxlarge');
				$oSupportPoint->setRequired(true);

				$oWalkDate->setAttrib('class', 'input-xlarge cDatePicker');
				$oWalkDate->setAttrib('data-date-format', 'dd-mm-yyyy');
				$oWalkDate->setAttrib('data-date-autoclose', 'true');

				$oWalkDate->addValidator(
						'Date',
						true,
						array(
							'format' => 'dd-MM-YYYY',
							'messages' => array(
								\Zend_Validate_Date::INVALID => "'%value%' lijkt geen geldige datum te zijn",
								\Zend_Validate_Date::INVALID_DATE => "'%value%' lijkt geen geldige datum te zijn",
								\Zend_Validate_Date::FALSEFORMAT => "'%value%' lijkt geen geldige datum te zijn",
							),

						)
					);
                $oWalkDateMinValidator = new \Zend_Validate_Callback(function($value){
                    if(strtotime($value) <= strtotime(date('Y-m-d'))){
                        return false;
                    }else{
                        return true;
                    }
                });
				$oWalkDate->addValidator($oWalkDateMinValidator);
				$oWalkCity->setAttrib('class', 'input-xlarge');

				$oComments->setAttrib('rows', 10);
				$oComments->setAttrib('class', 'input-xxlarge');

				$oBatch->setAttrib('class', 'input-medium');
				$oBatch->setRequired(true);
				$oBatch->addValidator('Int');
				$oBatch->addValidator('Between', true, array('min' => 2000, 'max' => ((int)date("Y") + 1)));

				$oLatitude->setAttrib('class', 'input-xlarge');
//				$oLatitude->setRequired(true);
//				$oLatitude->addValidator('Float', true);
//				$oLatitude->addValidator('Between', true, array('min' => -90, 'max' => 90));

				$oLongitude->setAttrib('class', 'input-xlarge');
//				$oLongitude->setRequired(true);
//				$oLongitude->addValidator('Float', true);
//				$oLongitude->addValidator('Between', true, array('min' => -180, 'max' => 180));

				$oSubmit->setLabel('<i class="icon icon-save"></i>&nbsp; Save')
						->setAttrib('escape', false)
						->setAttrib('class', 'btn btn-success');

				$aUpdateElements = array(
					$oName,
					$oAddress,
					$oCity,
					$oPostalCode,
					$oContactName,
					$oEmail,
					$oPhone,

					$oParticipatedBefore,
					$oParticipatedLastYear,
					$oParticipatedYearBeforeLast,
					$oParticipatedBeforeTheLast2Years,

					$oGrade7Groups,
					$oGrade8Groups,
					$oGrade67Groups,
					$oGrade678Groups,
					$oGrade78Groups,
					$oTotalStudents,

					$oSupportPoint,
					$oWalkDate,
					$oWalkCity,

					$oComments,

					$oBatch,
					$oLatitude,
					$oLongitude,

					$oSubmit,
				);
				$this->addElements($aUpdateElements);

				$this->_sViewScript = '/forms/registrant_manage.phtml';

				break;

			case self::CONTEXT_DELETE:

				$oName->setAttrib('class', 'input-xxlarge');
				$oName->setAttrib('readonly', true);

				$oAddress->setAttrib('class', 'input-xxlarge');
				$oAddress->setAttrib('readonly', true);

				$oCity->setAttrib('class', 'input-xlarge');
				$oCity->setAttrib('readonly', true);

				$oPostalCode->setAttrib('class', 'input-xlarge');
				$oPostalCode->setAttrib('readonly', true);

				$oContactName->setAttrib('class', 'input-xxlarge');
				$oContactName->setAttrib('readonly', true);
				$oEmail->setAttrib('class', 'input-xlarge');
				$oEmail->setAttrib('readonly', true);
				$oPhone->setAttrib('class', 'input-xlarge');
				$oPhone->setAttrib('readonly', true);

				$oParticipatedBefore->setSeparator(' ');
				$oParticipatedBefore->setAttrib('readonly', true);
				$oParticipatedLastYear->setAttrib('readonly', true);
				$oParticipatedYearBeforeLast->setAttrib('readonly', true);
				$oParticipatedBeforeTheLast2Years->setAttrib('readonly', true);

				$oGrade7Groups->setAttrib('class', 'input-medium');
				$oGrade7Groups->setAttrib('readonly', true);
				$oGrade8Groups->setAttrib('class', 'input-medium');
				$oGrade8Groups->setAttrib('readonly', true);
				$oGrade67Groups->setAttrib('class', 'input-medium');
				$oGrade67Groups->setAttrib('readonly', true);
				$oGrade678Groups->setAttrib('class', 'input-medium');
				$oGrade678Groups->setAttrib('readonly', true);
				$oGrade78Groups->setAttrib('class', 'input-medium');
				$oGrade78Groups->setAttrib('readonly', true);

				$oTotalStudents->setAttrib('class', 'input-medium');
				$oTotalStudents->setAttrib('readonly', true);

				$oSupportPoint->setAttrib('class', 'input-xxlarge');
				$oSupportPoint->setAttrib('readonly', true);

				$oWalkDate->setAttrib('class', 'input-xlarge');
				$oWalkDate->setAttrib('readonly', true);
				$oWalkCity->setAttrib('class', 'input-xlarge');
				$oWalkCity->setAttrib('readonly', true);

				$oComments->setAttrib('rows', 10);
				$oComments->setAttrib('class', 'input-xxlarge');
				$oComments->setAttrib('readonly', true);

				$oBatch->setAttrib('class', 'input-medium');
				$oBatch->setAttrib('readonly', true);
				$oLatitude->setAttrib('class', 'input-xlarge');
				$oLatitude->setAttrib('readonly', true);
				$oLongitude->setAttrib('class', 'input-xlarge');
				$oLongitude->setAttrib('readonly', true);

				$oSubmit->setLabel('<i class="icon icon-trash"></i>&nbsp; Remove')
						->setAttrib('escape', false)
						->setAttrib('class', 'btn btn-danger')
						->setAttrib('data-toggle', 'modal')
						->setAttrib('data-target', '#iDivModalRemoveRegistrant');

				$aDeleteElements = array(
					$oName,
					$oAddress,
					$oPostalCode,
					$oCity,
					$oContactName,
					$oEmail,
					$oPhone,

					$oParticipatedBefore,
					$oParticipatedLastYear,
					$oParticipatedYearBeforeLast,
					$oParticipatedBeforeTheLast2Years,

					$oGrade7Groups,
					$oGrade8Groups,
					$oGrade67Groups,
					$oGrade678Groups,
					$oGrade78Groups,
					$oTotalStudents,

					$oSupportPoint,
					$oWalkDate,
					$oWalkCity,

					$oComments,

					$oBatch,
					$oLatitude,
					$oLongitude,

					$oSubmit,
				);
				$this->addElements($aDeleteElements);

				$this->_sViewScript = '/forms/registrant_manage.phtml';

				break;

		}

		if ($this->_sViewScript == '') {
			$this->_sViewScript = '/forms/generic_two_column.phtml';
		}

		$this->setDecorators(array(array('ViewScript', array('viewScript' => $this->_sViewScript))));

	}

	public function getSupportPointOptions () {
        $oDao = new DaoRegistry();
        $aData = $oDao->fetchSupportpoints();
        $aOptions = array();
        foreach($aData AS $aSupportPoint){
            $aOptions[$aSupportPoint['id']]=$aSupportPoint['name'];
        }
		

		return $aOptions;

	}

}