<?php

/**
 * Editable Spam Protecter Field. Used with the User Defined Forms module (if 
 * installed) to allow the user to have captcha fields with their custom forms
 * 
 * @package SpamProtection
 */

class EditableSpamProtectionField extends EditableFormField {
	
	static $singular_name = 'Spam Protection Field';
	static $plural_name = 'Spam Protection Fields';
	
	function __construct( $record = null, $isSingleton = false ) {

		parent::__construct( $record, $isSingleton );
	}
	
	function getFormField() {
		return $this->createField();
	}
	
	function getFilterField() {
		return $this->createField(true);
	}
	
	function createField() {
		if($protector = SpamProtectorManager::get_spam_protector()) {
			if($protector) {
				$protector = new $protector();
				if($class = $protector->getFieldName()) {
					return new $class($class, $this->Title);
				}
			}
		}
		return false;
	}
	
	/**
	 * @return string
	 */
	public function Icon() {
		return 'spamprotection/images/' . strtolower($this->class) . '.png';
	}
	
	function showInReports() {
		return false;
	}
}
