<?php

/** 
 * This class is responsible for setting an system-wide spam protector field 
 * and add the protecter field to a form.
 * 
 * @package spamprotection
 */

class SpamProtectorManager {
	
	/**
	 * Current Spam Protector used on the site
	 *
	 * @var SpamProtector
	 */
	private static $spam_protector = null;
	
	/**
	 * Set the name of the spam protecter class
	 * 
	 * @param String the name of protecter field class
	 */
	public static function set_spam_protector($protector) {
		self::$spam_protector = $protector;
	}
	
	/**
	 * Get the name of the spam protector class
	 */
	public static function get_spam_protector() {
		return self::$spam_protector;
	}
	
	/**
	 * Add the spam protector field to a form
	 * @param 	Form 	the form that the protecter field added into 
	 * @param 	string	the name of the field that the protecter field will be added in front of
	 * @param 	array 	an associative array 
	 * 					with the name of the spam web service's field, for example post_title, post_body, author_name
	 * 					and a string of field names
	 * @param 	String 	Title for the captcha field
	 * @param 	String 	RightTitle for the captcha field
	 * @return 	SpamProtector 	object on success or null if the spamprotector class is not found 
	 *							also null if spamprotectorfield creation fails. 					
	 */
	static function update_form($form, $before = null, $fieldsToSpamServiceMapping = array(), $title = null, $rightTitle = null) {
		$protectorClass = self::get_spam_protector();
		
		// Don't update if no protector is set
		if(!$protectorClass) return false;
		
		if(!class_exists($protectorClass)) {
			return user_error("Spam Protector class '$protectorClass' does not exist. Please define a valid Spam Protector", E_USER_WARNING);
		}
		
		try {
			$protector = new $protectorClass();
			$field = $protector->getFormField("Captcha", $title, null, $form, $rightTitle);
			
			if($field) {
				// update the mapping
				$field->setFieldMapping($fieldsToSpamServiceMapping);
				
				// add the form field
				if($before && $form->Fields()->fieldByName($before)) {
					$form->Fields()->insertBefore($field, $before);
				}
				else {
					$form->Fields()->push($field);
				}	
			}
			
		} catch (Exception $e) {
			return user_error("SpamProtectorManager::update_form(): '$protectorClass' is not correctly set up. " . $e, E_USER_WARNING);
		}
	}
	
	/**
	 * Send Feedback to the Spam Protection. The level of feedback
	 * will depend on the Protector class.
	 *
	 * @param DataObject The Object which you want to send feedback about. Must have a 
	 *						SessionID field.
	 * @param String Feedback on the $object usually 'spam' or 'ham' for non spam entries
	 */
	static function send_feedback($object, $feedback) {
		$protectorClass = self::get_spam_protector();
		
		if(!$protectorClass) return false;
		
		$protector = new $protectorClass();
		return $protector->sendFeedback($object, $feedback);
	}
}