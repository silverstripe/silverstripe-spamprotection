<?php

/**
 * @package spamprotection
 */
class FormSpamProtectionExtensionTest_FooProtector implements SpamProtector, TestOnly
{
    public function getFormField($name = null, $title = null, $value = null)
    {
        return new TextField($name, 'Foo', $value);
    }

    public function setFieldMapping($fieldMapping)
    {
    }
}
