<?php

/**
 * Spam Protector base interface. All Protectors should implement this interface 
 * to ensure that they contain all the correct methods and we do not get too many
 * odd missing function errors
 * 
 * @package SpamProtection
 */

interface SpamProtector {
	
	public function sendFeedback($object = null, $feedback = "");
	
	public function updateForm($form, $before = null, $fieldsToSpamServiceMapping = null);
	
	public function setFieldMapping($fieldToPostTitle, $fieldsToPostBody = null, $fieldToAuthorName = null, $fieldToAuthorUrl = null, $fieldToAuthorEmail = null, $fieldToAuthorOpenId = null);
}
?>