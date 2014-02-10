<?php

/**
 * Editable Spam Protecter Field. Used with the User Defined Forms module (if 
 * installed) to allow the user to have captcha fields with their custom forms
 * 
 * @package spamprotection
 */
if(class_exists('EditableFormField')) {
	
	class EditableSpamProtectionField extends EditableFormField {
	
		static $singular_name = 'Spam Protection Field';
	
		static $plural_name = 'Spam Protection Fields';
	
		public function getFormField() {
			if($protector = Config::inst()->get('FormSpamProtectionExtension', 'default_spam_protector')) {
				$protector = Injector::inst()->create($protector);
					
				return $protector->getFormField($this->Name, $this->Title, null);
			}
			
			return false;
		}
		
		public function getFieldValidationOptions() {
			return new FieldList();
		}
		
		public function getRequired() {
			return false;
		}

		public function Icon() {
			return 'spamprotection/images/' . strtolower($this->class) . '.png';
		}
	
		public function showInReports() {
			return false;
		}
	}
}