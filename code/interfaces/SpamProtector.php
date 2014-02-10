<?php

/**
 * SpamProtector base interface. 
 *
 * All Protectors are required implement this interface if they want to appear
 * on the form.
 *
 * Classes with this interface are used to generate helper lists to allow the
 * user to select the protector.
 * 
 * @package spamprotection
 */

interface SpamProtector {
	
	/**
	 * Return the {@link FormField} associated with this protector.
	 *
	 * Most spam methods will simply return a piece of HTML to be injected at 
	 * the end of the form. If a spam method needs to inject more than one
	 * form field (i.e a hidden field and a text field) then return a 
	 * {@link FieldGroup} from this method to include both.
	 *
	 * @param string $name
	 * @param string $title
	 * @param mixed $value
	 */
	public function getFormField($name = null, $title = null, $value = null);
}