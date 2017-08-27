<?php

/**
 * @package spamprotection
 */
class FormSpamProtectionExtensionTest_BazProtector implements SpamProtector, TestOnly
{
    public function getFormField($name = null, $title = null, $value = null)
    {
        return new TextField($name, $title, $value);
    }

    public function setFieldMapping($fieldMapping)
    {
    }
}
