<?php

class EditableSpamProtectionFieldTest extends SapphireTest
{

    protected $usesDatabase = true;

    public function setUp()
    {
        parent::setUp();

        if (!class_exists('EditableSpamProtectionField')) {
            $this->markTestSkipped('"userforms" module not installed');
        }

        Config::inst()->update(
            'FormSpamProtectionExtension',
            'default_spam_protector',
            'EditableSpamProtectionFieldTest_Protector'
        );
    }

    public function testValidateFieldDoesntAddErrorOnSuccess()
    {
        $formMock = $this->getFormMock();
        $formFieldMock = $this->getEditableFormFieldMock();

        $formFieldMock
            ->getFormField() // mock
            ->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(true));

        $formMock
            ->expects($this->never())
            ->method('addErrorMessage');

        $formFieldMock->validateField(array('MyField' => null), $formMock);
    }

    public function testValidateFieldAddsErrorFromField()
    {
        $formMock = $this->getFormMock();
        $formFieldMock = $this->getEditableFormFieldMock();

        $formFieldMock
            ->getFormField() // mock
            ->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(false));

        $formMock->getValidator()->validationError('MyField', 'some field message', 'required');

        $formMock
            ->expects($this->once())
            ->method('addErrorMessage')
            ->with($this->anything(), $this->stringContains('some field message'), $this->anything(), $this->anything());
        ;

        $formFieldMock->validateField(array('MyField' => null), $formMock);
    }

    public function testValidateFieldAddsDefaultError()
    {
        $formMock = $this->getFormMock();
        $formFieldMock = $this->getEditableFormFieldMock();

        $formFieldMock
            ->getFormField() // mock
            ->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(false));

        // field doesn't set any validation errors here

        $formMock
            ->expects($this->once())
            ->method('addErrorMessage')
            ->with($this->anything(), $this->stringContains('default error message'), $this->anything(), $this->anything());

        $formFieldMock->validateField(array('MyField' => null), $formMock);
    }

    protected function getFormMock()
    {
        $formMock = $this->getMockBuilder('Form', array('addErrorMessage'))
            ->disableOriginalConstructor()
            ->getMock();
        $formMock
            ->expects($this->any())
            ->method('getValidator')
            ->will($this->returnValue(new RequiredFields()));

        return $formMock;
    }

    protected function getEditableFormFieldMock()
    {
        $page = new UserDefinedForm();
        $page->write();

        $formFieldMock = $this->getMockBuilder('TextField')
            ->disableOriginalConstructor()
            ->getMock();

        $editableFormFieldMock = new EditableSpamProtectionField(array(
            'ParentID' => $page->ID,
            'Name' => 'MyField',
            'CustomErrorMessage' => 'default error message'
        ));
        $editableFormFieldMock->write();
        $editableFormFieldMock->setFormField($formFieldMock);

        return $editableFormFieldMock;
    }
}
