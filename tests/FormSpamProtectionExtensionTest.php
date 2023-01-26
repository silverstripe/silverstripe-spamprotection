<?php

namespace SilverStripe\SpamProtection\Tests;

use LogicException;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\TextField;
use SilverStripe\SpamProtection\Extension\FormSpamProtectionExtension;
use SilverStripe\SpamProtection\Tests\Stub\FooProtector;
use SilverStripe\SpamProtection\Tests\Stub\BarProtector;
use SilverStripe\SpamProtection\Tests\Stub\BazProtector;

/**
 * @package spamprotection
 */
class FormSpamProtectionExtensionTest extends SapphireTest
{
    protected $usesDatabase = false;

    /**
     * @var Form
     */
    protected $form = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->form = new Form(new Controller, 'Form', new FieldList(
            new TextField('Title'),
            new TextField('Comment'),
            new TextField('URL')
        ), new FieldList());

        $this->form->disableSecurityToken();
    }

    public function testEnableSpamProtectionThrowsException()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('No spam protector has been set. Null is not valid value.');

        Config::modify()->set(FormSpamProtectionExtension::class, 'default_spam_protector', null);
        $this->form->enableSpamProtection();
    }

    public function testEnableSpamProtection()
    {
        Config::modify()->set(
            FormSpamProtectionExtension::class,
            'default_spam_protector',
            FooProtector::class
        );

        $form = $this->form->enableSpamProtection();

        $this->assertEquals('Foo', $form->Fields()->fieldByName('Captcha')->Title());
    }

    public function testEnableSpamProtectionCustomProtector()
    {
        $form = $this->form->enableSpamProtection(array(
            'protector' => BarProtector::class
        ));

        $this->assertEquals('Bar', $form->Fields()->fieldByName('Captcha')->Title());
    }

    public function testEnableSpamProtectionCustomTitle()
    {
        $form = $this->form->enableSpamProtection(array(
            'protector' => BarProtector::class,
            'title' => 'Baz',
        ));

        $this->assertEquals('Baz', $form->Fields()->fieldByName('Captcha')->Title());
    }

    public function testCustomOptions()
    {
        $form = $this->form->enableSpamProtection(array(
            'protector' => BazProtector::class,
            'title' => 'Qux',
            'name' => 'Borris'
        ));

        $this->assertEquals('Qux', $form->Fields()->fieldByName('Borris')->Title());
    }

    public function testConfigurableName()
    {
        $field_name = "test_configurable_name";
        Config::modify()->set(
            FormSpamProtectionExtension::class,
            'default_spam_protector',
            FooProtector::class
        );
        Config::modify()->set(
            FormSpamProtectionExtension::class,
            'field_name',
            $field_name
        );
        $form = $this->form->enableSpamProtection();
        // remove for subsequent tests
        Config::modify()->remove(FormSpamProtectionExtension::class, 'field_name');
        // field should take up configured name
        $this->assertEquals('Foo', $form->Fields()->fieldByName($field_name)->Title());
    }

    public function testInsertBefore()
    {
        $form = $this->form->enableSpamProtection(array(
            'protector' => FooProtector::class,
            'insertBefore' => 'URL'
        ));

        $fields = $form->Fields();
        $this->assertEquals('Title', $fields[0]->Title());
        $this->assertEquals('Comment', $fields[1]->Title());
        $this->assertEquals('Foo', $fields[2]->Title());
        $this->assertEquals('URL', $fields[3]->Title());
    }

    public function testInsertBeforeMissing()
    {
        $form = $this->form->enableSpamProtection(array(
            'protector' => FooProtector::class,
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
