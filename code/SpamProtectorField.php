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
 * @package spamprotection
 */
class SpamProtectorField extends FormField {
	
	protected $spanControlCallbackObj = null;
	
	/**
	 * Set the Callback Object
	 */
	function setCallbackObject($callbackObject) {
		$this->spanControlCallbackObj = $callbackObject;
	}
 
	/**
	 * Tell the callback object the submission is spam
	 */ 
	protected function markAsSpam() {
		if ($this->spanControlCallbackObj && $this->spanControlCallbackObj instanceof Spamable) {
			$this->spanControlCallbackObj->markAsSpam($this->getForm());
		}
	}
	
	/**
	 * Tell the callback object the submission is ham
	 */
	protected function markAsHam() {
		if ($this->spanControlCallbackObj && $this->spanControlCallbackObj instanceof Spamable) {
			$this->spanControlCallbackObj->markAsHam($this->getForm());
		}
	}
	
	
}
?>
