<?php

/**
 * @package spamprotection
 */
class FormSpamProtectionExtensionTest extends SapphireTest {
	
	public function setUp() {
		parent::setUp();

		$this->form = new Form($this, 'Form', new FieldList(
			new TextField('Title'),
			new TextField('Comment'),
			new TextField('URL')
		), new FieldList()
		);
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

		$protector = new FormSpamProtectionExtensionTest_BarProtector();
		$protector->title = "Baz";

		$form = $this->form->enableSpamProtection(array(
			'protector' => $protector
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
}

/**
 * @package spamprotection
 */
class FormSpamProtectionExtensionTest_BazProtector implements SpamProtector, TestOnly {

	public function getFormField($name = null, $title = null, $value = null) {
		return new TextField($name, $title, $value);
	}
}

/**
 * @package spamprotection
 */
class FormSpamProtectionExtensionTest_BarProtector implements SpamProtector, TestOnly {

	public $title = 'Bar';

	public function getFormField($name = null, $title = null, $value = null) {
		return new TextField($name, $this->title, $value);
	}
}

/**
 * @package spamprotection
 */
class FormSpamProtectionExtensionTest_FooProtector implements SpamProtector, TestOnly {

	public function getFormField($name = null, $title = null, $value = null) {
		return new TextField($name, 'Foo', $value);
	}
}