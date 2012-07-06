<?php
/**
 * This class acts as a template for spam protecting form field, for instance MollomField.
 *
 * @package spamprotection
 */
abstract class SpamProtectorField extends FormField {
	
	/**
	 * Fields to map spam protection too.
	 *
	 * @var array
	 */
	private $spamFieldMapping = array();
    
    function __construct($name, $title = null, $value = null, $form = null, $rightTitle = null) {
		parent::__construct($name, $title, $value);
        
        if(!empty($form)) {
            $this->form=$form;
        }
    }
	
	
	/**
	 * Set the fields to map spam protection too
	 * 
	 * @param Array array of Field Names, where the indexes of the array are the field names of the form and the values are the field names of the spam/captcha service
	 */
	public function setFieldMapping($array) {
		$this->spamFieldMapping = $array;
	}
	
	/**
	 * Get the fields that are mapped via spam protection
	 *
	 * @return Array
	 */
	public function getFieldMapping() {
		return $this->spamFieldMapping;
	}
}