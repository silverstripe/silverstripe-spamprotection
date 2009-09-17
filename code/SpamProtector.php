<?php

/**
 * Spam Protector base interface. All Protectors should implement this interface 
 * to ensure that they contain all the correct methods.
 * 
 * @package spamprotection
 */

interface SpamProtector {
	
	/**
	 * Return the Field Associated with this protector
	 */
	public function getFormField($name = null, $title = null, $value = null, $form = null, $rightTitle = null);
	
	/**
	 * Function required to handle dynamic feedback of the system.
	 * if unneeded just return true
	 */
	public function sendFeedback($object = null, $feedback = "");
	
}