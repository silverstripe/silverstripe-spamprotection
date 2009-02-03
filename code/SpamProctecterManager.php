<?php
/** 
 * This class is responsible for setting an system-wide spam protecter field 
 * and add the protecter field to a form
 */
class SpamProctecterManager {
	
	static $spam_protecter = null;
	
	/**
	 * Set the name of the spam protecter class
	 * @param 	string 	the name of protecter field class
	 */
	static function set_spam_protecter($protecter) {
		self::$spam_protecter = $protecter;
	}
	
	/**
	 * Add the spam protecter field to a form
	 * @param  	string 	the name of the protecter field
	 * @param 	string	the title of the protecter field
	 * @param 	Form 	the form that the protecter field added into 
	 * @param 	string	the name of the field that the protecter field will be added in front of
	 * @param 	object	an object that implements Spamable
	 * @param 	array 	an associative array 
	 * 					with the name of the spam web service's field, for example post_title, post_body, author_name
	 * 					and a string of field names (seperated by comma) as a value.
	 *                 	The naming of the fields is based on the implementation of the subclass of SpamProtecterField.
	 * 					*** Most of the web service doesn't require this.   
	 * @return 	SpamProtecterField					
	 */
	static function update_form($protecterFieldName, $protecterFieldTitle, $form, $before=null, $callbackObject=null, $fieldsToSpamServiceMapping=null) {
	
		$protecterField = new self::$spam_protecter($protecterFieldName, $protecterFieldTitle);
		$protecterField->setCallbackObject($callbackObject);
		
		if ($before && $fields->fieldByName($before)) {
			$form->Fields()->insertBefore($protecterField, $before);
		}
		else {
			$form->Fields()->push($protecterField);
		}
		
		return $protecterField;
	}
}
?>