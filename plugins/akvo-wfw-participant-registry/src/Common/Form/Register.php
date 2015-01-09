<?php
namespace Akvo\WfW\ParticipantRegistry\Common\Form;

use KwgPress as KwgP;
use Akvo\WfW\ParticipantRegistry\Common\Model\Dao\ParticipantRegistry as DaoRegistry;
/**
 * Description of Definition
 *
 * @author Jayawi Perera
 */
class Register extends KwgP\Form {

	protected $_sPluginSlug = AkvoWfwParticipantRegistry_Plugin_Slug;

	public function init() {

		$oId = new \Zend_Form_Element_Hidden('hiddenId');

		$oName = new \Zend_Form_Element_Text('textName');
		$oName->setLabel('Name organisation / school');
		$oSupportOrganisation = new \Zend_Form_Element_Text('textSupportOrganisation');
		$oSupportOrganisation->setLabel('Name of the support center organisation');

        $oContactName = new \Zend_Form_Element_Text('textContactName');
		$oContactName->setLabel('Name contact person');
        
        $oCountry = new \Zend_Form_Element_Text('textCountry');
		$oCountry->setLabel('Country');

		$oEmail = new \Zend_Form_Element_Text('textEmail');
		$oEmail->setLabel('Email address');
        
		$oConfirmEmail = new \Zend_Form_Element_Text('textConfirmEmail');
		$oConfirmEmail->setLabel('Confirm your email address');
        
		$oAddress1 = new \Zend_Form_Element_Text('textAddress1');
		$oAddress1->setLabel('Postal address line 1');
        
		$oAddress2 = new \Zend_Form_Element_Text('textAddress2');
		$oAddress2->setLabel('line 2');
        
		$oAddress3 = new \Zend_Form_Element_Text('textAddress3');
		$oAddress3->setLabel('line 3');

		$oWalkDate = new  \Zend_Form_Element_Text('textWalkDate');
		$oWalkDate->setLabel('Planned date of event');

		$oTotalSchools = new \Zend_Form_Element_Text('textTotalSchools');
		$oTotalSchools->setLabel('How many schools will participate');

		$oTotalStudents = new \Zend_Form_Element_Text('textTotalStudents');
		$oTotalStudents->setLabel('How many children will participate in total');


		$oProject = new \Zend_Form_Element_Select('selectProject');
		$oProject->setLabel('Supported project');
		$oProject->setMultiOptions($this->getProjectOptions());

		$oAgree = new \Zend_Form_Element_Checkbox('checkboxAgree');
		$oAgree->setLabel('I have read and accept the disclaimer');

		$oComments = new \Zend_Form_Element_Textarea('textComments');
		$oComments->setLabel('Comments');

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
                $oSupportOrganisation->setRequired(true);
				$oAddress1->setRequired(true);
                $oCountry->setRequired(true);
				$oContactName->setRequired(true);

				$oEmail->setRequired(true);
				$oEmail->addValidator('EmailAddress');

				$oConfirmEmail->setRequired(true);
                $valid = new \Zend_Validate_Identical(array('token' => 'textEmail', 'strict' => FALSE));
                $oConfirmEmail->addValidator($valid); 

				$oTotalSchools->setRequired(true);
				$oTotalSchools->addValidator('Int');
				$oTotalStudents->setRequired(true);
				$oTotalStudents->addValidator('Int');

				$oProject->setRequired(true);

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
							\Zend_Validate_InArray::NOT_IN_ARRAY => "You need to accept the disclaimer",
							),
						)
					);

				$oSubmit->setLabel('Submit');

				$aCreateElements = array(
					$oName,
                    $oSupportOrganisation,
					$oContactName,
                    $oCountry,
                    $oEmail,
					$oConfirmEmail,
					$oAddress1,
					$oAddress2,
					$oAddress3,
					$oWalkDate,
					$oTotalSchools,
					$oTotalStudents,

					$oProject,
					
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
                
				$oSupportOrganisation->setAttrib('class', 'input-xxlarge');
				$oSupportOrganisation->setRequired(true);
                $oContactName->setAttrib('class', 'input-xxlarge');
				$oContactName->setRequired(true);
				
                $oCountry->setAttrib('class', 'input-xxlarge');
				$oCountry->setRequired(true);
				
				$oAddress1->setAttrib('class', 'input-xxlarge');
				$oAddress1->setRequired(true);
				
				$oAddress2->setAttrib('class', 'input-xxlarge');
				
				$oAddress2->setAttrib('class', 'input-xxlarge');
				


				$oEmail->setAttrib('class', 'input-xlarge');
				$oEmail->setRequired(true);
				$oEmail->addValidator('EmailAddress');
				
				

				$oTotalSchools->setAttrib('class', 'input-medium');
				$oTotalSchools->setRequired(true);
				$oTotalSchools->addValidator('Int');
				$oTotalStudents->setAttrib('class', 'input-medium');
				$oTotalStudents->setRequired(true);
				$oTotalStudents->addValidator('Int');

				$oProject->setAttrib('class', 'input-xxlarge');
				$oProject->setRequired(true);

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
                    $oSupportOrganisation,
                    $oContactName,
                    $oCountry,
                    $oEmail,
                    $oAddress1,
                    $oAddress2,
                    $oAddress3,
					
					$oWalkDate,
					$oTotalSchools,
                    $oTotalStudents,
                    $oProject,

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

				

				$oSubmit->setLabel('<i class="icon icon-trash"></i>&nbsp; Remove')
						->setAttrib('escape', false)
						->setAttrib('class', 'btn btn-danger')
						->setAttrib('data-toggle', 'modal')
						->setAttrib('data-target', '#iDivModalRemoveRegistrant');

				$aDeleteElements = array(
					$oName,
					

					$oSubmit,
				);
				$this->addElements($aDeleteElements);

				$this->_sViewScript = '/forms/registrant_delete.phtml';

				break;

		}

		if ($this->_sViewScript == '') {
			$this->_sViewScript = '/forms/generic_two_column.phtml';
		}

		$this->setDecorators(array(array('ViewScript', array('viewScript' => $this->_sViewScript))));

	}

	public function getProjectOptions () {
        global $wpdb;
        $aOptions = array(0=>'select');
        $aProjects = $wpdb->get_results( 
                            "
                            SELECT title, project_id 
                            FROM ".$wpdb->prefix."projects
                            ORDER BY title ASC
                            "
                        );
        
        foreach($aProjects AS $oProject){
            $aOptions[$oProject->project_id]=$oProject->title;
        }
		

		return $aOptions;

	}

}