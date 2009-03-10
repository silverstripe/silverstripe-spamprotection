<?php
/** 
 * This class is responsible for setting an system-wide spam protecter field 
 * and add the protecter field to a form
 */
class SpamProtecterManager {
	
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
	 * @param 	Form 	the form that the protecter field added into 
	 * @param 	string	the name of the field that the protecter field will be added in front of
	 * @param 	object	an object that implements Spamable
	 * @param 	array 	an associative array 
	 * 					with the name of the spam web service's field, for example post_title, post_body, author_name
	 * 					and a string of field names (seperated by comma) as a value.
	 *                 	The naming of the fields is based on the implementation of the subclass of SpamProtecterField.
	 * 					*** Most of the web service doesn't require this.   
	 * @return 	SpamProtector 	object on success or null if the spamprotecter class is not found 
	 *							also null if spamprotecterfield creation fails. 					
	 */
	static function update_form($form, $before=null, $callbackObject=null, $fieldsToSpamServiceMapping=null) {
		if (!class_exists(self::$spam_protecter)) return null;
		
		$protecter = new self::$spam_protecter();
		try {
			$check = $protecter->updateForm($form, $before, $callbackObject, $fieldsToSpamServiceMapping);
		}
		catch (Exception $e) {
			$form->setMessage(
				_t("SpamProtection.SPAMSPECTIONFAILED", self::$spam_protecter . " failed."),
				"warning"
				);
			return null;
		}
		
		if (!$check) return null;
		
		return $protecter;
	}
	
	/**
	 * Mark a DataObject as spam
	 *
	 * @param DataObject
	 */
	static function mark_spam($object) {
		$protecter = new self::$spam_protecter();
		return $protecter->markAsSpam($object);
	}
}
?>