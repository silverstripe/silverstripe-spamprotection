<?php

/**
 * An extension to the {@link Form} class which provides the method 
 * {@link enableSpamProtection()} helper.
 *
 * @package spamprotection
 */

class FormSpamProtectionExtension extends Extension {
	
	/**
	 * @var array
	 */
	private $fieldMapping = array();

	/**
	 * @config
	 *
	 * The default spam protector class name to use. Class should implement the
	 * {@link SpamProtector} interface.
	 *
	 * @var string $spam_protector
	 */
	private static $default_spam_protector;

	/**
	 * @config
	 *
	 * The {@link enableSpamProtection} method will define which of the form 
	 * values correlates to this form mapped fields list. Totally custom forms
	 * and subclassed SpamProtector instances are define their own mapping
	 *
	 * @var array $mappable_fields
	 */
	private static $mappable_fields =  array(
		'title',
		'body',
		'contextUrl',
		'contextTitle',
		'authorName',
		'authorMail',
		'authorUrl',
		'authorIp',
		'authorId'
	);

	/**
	 * Activates the spam protection module.
	 *
	 * @param array $options
	 */
	public function enableSpamProtection($options = array()) {
		// generate the spam protector
		if(isset($options['protector'])) {
			$protector = $options['protector'];

			if(is_string($protector)) {
				$protector = Injector::inst()->create($protector);
			}
		} else {
			$protector = Config::inst()->get('FormSpamProtectionExtension', 'default_spam_protector');
			$protector = Injector::inst()->create($protector);
		}
		
		// captcha form field name (must be unique)
		if(isset($options['name'])) {
			$name = $options['name'];
		} else {
			$name = 'Captcha';
		}

		// captcha field title
		if(isset($options['title'])) {
			$title = $options['title'];
		} else {
			$title = '';
		}

		// set custom mapping on this form
		if(isset($options['mapping'])) {
			$this->fieldMapping = $options['mapping'];	
		}

		// add the form field
		if($field = $protector->getFormField($name, $title)) {
			$this->owner->Fields()->push($field);
		}
	
		return $this->owner;
	}

	/**
	 * @return bool
	 */
	public function hasSpamProtectionMapping() {
		return ($this->fieldMapping);
	}
	
	/**
	 * @return array
	 */
	public function getSpamMappedData() {
		if($this->fieldMapping) {
			$result = array();
			$data = $this->owner->getData();

			foreach($this->fieldMapping as $fieldName => $mappedName) {
				$result[$mappedName] = (isset($data[$fieldName])) ? $data[$fieldName] : null;
			}

			return $result;
		}

		return null;
	}
}