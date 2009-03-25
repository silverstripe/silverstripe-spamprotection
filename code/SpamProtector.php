<?php

/**
 * Spam Protector base interface. All Protectors should implement this interface 
 * to ensure that they contain all the correct methods and we do not get too many
 * odd missing function errors
 * 
 * @package SpamProtection
 */

interface SpamProtector {
	
	/**
	 * Return the name of the Field Associated with this protector
	 */
	public function getFieldName();
	
	/**
	 * Function required to handle dynamic feedback of the system.
	 * if unneeded just return true
	 */
	public function sendFeedback($object = null, $feedback = "");
	
	/**
	 * Updates the form with the given protection
	 */
	public function updateForm($form, $before = null, $fieldsToSpamServiceMapping = null);
	
	/**
	 * Set which fields need to be mapped for protection
	 */
	public function setFieldMapping($fieldToPostTitle, $fieldsToPostBody = null, $fieldToAuthorName = null, $fieldToAuthorUrl = null, $fieldToAuthorEmail = null, $fieldToAuthorOpenId = null);
}
?>