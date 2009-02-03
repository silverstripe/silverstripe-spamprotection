<?php
/**
 * This class acts as a template for spam protecting form field, for instance MollomField.
 * It provides a number of properties for mapping fields of the form which this object belongs to
 * to spam checking service fields. 
 * 
 * In order to further process the form values or take any action according the status of spam checking,
 * markAsSpam() and markAsHam should be called in validate() after the status of the spam checking has
 * been obtained. 
 * 
 */
class SpamProtecterField_Backup extends FormField {
	
	protected $spanControlCallbackObj = null;
	
	function setCallbackObject($callbackObject) {
		$this->spanControlCallbackObj = $callbackObject;
	}
	
	/* Map fields (by name) to Spam service's fields for spam checking */
	protected $fieldToPostTitle = "";
	
	// it can be more than one fields mapped to post content
	protected $fieldsToPostBody = array();
	
	protected $fieldToAuthorName = "";
	
	protected $fieldToAuthorUrl = "";
	
	protected $fieldToAuthorEmail = "";
	
	protected $fieldToAuthorOpenId = "";
	
	function setFieldToPostTitle($fieldName) {
		$this->fieldToPostTitle = $fieldName;
	}
	
	/**
	 * Map array of fields where their value will be used as a mollom post body
	 * @param 	array
	 */
	function setFieldsToPostBody($fieldNames) {
		$this->fieldsToPostBody = $fieldNames;
	}
	
	function setfieldToAuthorName($fieldName) {
		$this->fieldToAuthorName = $fieldName;
	}
	
	function setFieldToAuthorUrl($fieldName) {
		$this->fieldToAuthorUrl = $fieldName;
	}
	
	function setFieldToAuthorEmail($fieldName) {
		$this->fieldToAuthorEmail = $fieldName;
	}
	
	function setFieldToAuthorOpenId($fieldName) {
		$this->fieldToAuthorOpenId = $fieldName;
	}
	
	/**
	 * Tell the callback object the spam checking response status 
	 */ 
	protected function markAsSpam() {
		if ($this->spanControlCallbackObj && $this->spanControlCallbackObj instanceof Spamable) {
			$this->spanControlCallbackObj->markAsSpam($this->getForm());
		}
	}
	
	/**
	 * Tell the callback object the spam checking response status 
	 */
	protected function markAsHam() {
		if ($this->spanControlCallbackObj && $this->spanControlCallbackObj instanceof Spamable) {
			$this->spanControlCallbackObj->markAsHam($this->getForm());
		}
	}
}
?>