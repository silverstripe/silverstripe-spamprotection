<?php

/**
 * Editable Spam Protecter Field. Used with the User Defined Forms module (if 
 * installed) to allow the user to have captcha fields with their custom forms
 * 
 * @package spamprotection
 */
if(class_exists('EditableFormField')){
	class EditableSpamProtectionField extends EditableFormField {
	
		static $singular_name = 'Spam Protection Field';
	
		static $plural_name = 'Spam Protection Fields';
	
		function getFormField() {
			if($protector = SpamProtectorManager::get_spam_protector()) {
				if($protector) {
					$protector = new $protector();
					
					return $protector->getFormField($this->Name, $this->Title, null);
				}
			}
			
			return false;
		}
		
		function getFieldValidationOptions() {
			return new FieldSet();
		}
		
		function getRequired() {
			return false;
		}

		public function Icon() {
			return 'spamprotection/images/' . strtolower($this->class) . '.png';
		}
	
		function showInReports() {
			return false;
		}
	}
}