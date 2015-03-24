<?php

/**
 * @package spamprotection
 */
class FormSpamProtectionExtensionTest extends SapphireTest {
	
	protected $usesDatabase = false;

	/**
	 * @var Form
	 */
	protected $form = null;
	
	public function setUp() {
		parent::setUp();

		$this->form = new Form($this, 'Form', new FieldList(
			new TextField('Title'),
			new TextField('Comment'),
			new TextField('URL')
		), new FieldList()
		);
		$this->form->disableSecurityToken();
	}

	public function testEnableSpamProtection() {
		Config::inst()->update(
			'FormSpamProtectionExtension', 'default_spam_protector', 
			'FormSpamProtectionExtensionTest_FooProtector'
		);

		$form = $this->form->enableSpamProtection();

		$this->assertEquals('Foo', $form->Fields()->fieldByName('Captcha')->Title());

	}

	public function testEnableSpamProtectionCustomProtector() {
		$form = $this->form->enableSpamProtection(array(
			'protector' => 'FormSpamProtectionExtensionTest_BarProtector'
		));

		$this->assertEquals('Bar', $form->Fields()->fieldByName('Captcha')->Title());
	}

	public function testEnableSpamProtectionCustomTitle() {
		$form = $this->form->enableSpamProtection(array(
			'protector' => 'FormSpamProtectionExtensionTest_BarProtector',
			'title' => 'Baz',
		));
		
		$this->assertEquals('Baz', $form->Fields()->fieldByName('Captcha')->Title());
	}

	public function testCustomOptions() {
		$form = $this->form->enableSpamProtection(array(
			'protector' => 'FormSpamProtectionExtensionTest_BazProtector',
			'title' => 'Qux',
			'name' => 'Borris'
		));

		$this->assertEquals('Qux', $form->Fields()->fieldByName('Borris')->Title());
	}
	
	public function testInsertBefore() {
		
		$form = $this->form->enableSpamProtection(array(
			'protector' => 'FormSpamProtectionExtensionTest_FooProtector',
			'insertBefore' => 'URL'
		));
		
		$fields = $form->Fields();
		$this->assertEquals('Title', $fields[0]->Title());
		$this->assertEquals('Comment', $fields[1]->Title());
		$this->assertEquals('Foo', $fields[2]->Title());
		$this->assertEquals('URL', $fields[3]->Title());
	}
	
	public function testInsertBeforeMissing() {
		
		$form = $this->form->enableSpamProtection(array(
			'protector' => 'FormSpamProtectionExtensionTest_FooProtector',
			'insertBefore' => 'NotAField'
		));
		
		// field should default to the end instead
		$fields = $form->Fields();
		$this->assertEquals('Title', $fields[0]->Title());
		$this->assertEquals('Comment', $fields[1]->Title());
		$this->assertEquals('URL', $fields[2]->Title());
		$this->assertEquals('Foo', $fields[3]->Title());
	}
	
}

/**
 * @package spamprotection
 */
class FormSpamProtectionExtensionTest_BazProtector implements SpamProtector, TestOnly {

	public function getFormField($name = null, $title = null, $value = null) {
		return new TextField($name, $title, $value);
	}

	public function setFieldMapping($fieldMapping) {}

}

/**
 * @package spamprotection
 */
class FormSpamProtectionExtensionTest_BarProtector implements SpamProtector, TestOnly {

	public function getFormField($name = null, $title = null, $value = null) {
		$title = $title ?: 'Bar';
		return new TextField($name, $title, $value);
	}

	public function setFieldMapping($fieldMapping) {}

}

/**
 * @package spamprotection
 */
class FormSpamProtectionExtensionTest_FooProtector implements SpamProtector, TestOnly {

	public function getFormField($name = null, $title = null, $value = null) {
		return new TextField($name, 'Foo', $value);
	}

	public function setFieldMapping($fieldMapping) {}

}