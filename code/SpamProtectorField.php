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
	
	
	/**
	 * Set the fields to map spam protection too
	 * 
	 * @param Array array of Field Names
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