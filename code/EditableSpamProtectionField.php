<?php

/**
 * Editable Spam Protecter Field. Used with the User Defined Forms module (if 
 * installed) to allow the user to have captcha fields with their custom forms
 * 
 * @package SpamProtection
 */

class EditableSpamProtectionField extends EditableFormField {
	
	static $db = array(

	);
	
	static $singular_name = 'Spam Protection Field';
	static $plural_name = 'Spam Protection Fields';
	
	function __construct( $record = null, $isSingleton = false ) {

		parent::__construct( $record, $isSingleton );
	}
	
	function ExtraOptions() {
		
		// eventually replace hard-coded "Fields"?
		$baseName = "Fields[$this->ID]";
		
		$extraFields = new FieldSet();
		
		foreach( parent::ExtraOptions() as $extraField )
			$extraFields->push( $extraField );
			
		if( $this->readonly )
			$extraFields = $extraFields->makeReadonly();	
			
		return $extraFields;		
	}
	
	function populateFromPostData($data) {
		parent::populateFromPostData($data);
	}
	
	function getFormField() {
		return $this->createField();
	}
	
	function getFilterField() {
		return $this->createField(true);
	}
	
	function createField() {
		if($protector = SpamProtecterManager::get_spam_protecter()) {
			if($protector) {
				$protector = new $protector();
				if($class = $protector->getFieldName()) {
					$spamProtection = new $class($class, $this->Title);
					if($spamProtection) {
						// set field mapping for all the fields in this form.
						// fields should have the same ParentID as this 
						$fields = DataObject::get("EditableTextField", "ParentID = '$this->ParentID'");
						$fields = ($fields) ? $fields->toArray('Name') : null;
						
						// @TODO get FieldMapping Working.
						$spamProtection->setFieldMapping(null, $fields);
						return $spamProtection;
					}
				}
			}
		}
		return false;
	}
	/**
	 * Populates the default fields. 
	 */
	function DefaultField() {
		return "";
	}
	
	/**
	 * @return string
	 */
	public function Icon() {
		return 'spamprotection/images/' . strtolower($this->class) . '.png';
	}
	
}