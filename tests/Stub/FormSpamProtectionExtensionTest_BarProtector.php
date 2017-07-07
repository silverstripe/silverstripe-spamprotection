<?php

/**
 * @package spamprotection
 */
class FormSpamProtectionExtensionTest_BarProtector implements SpamProtector, TestOnly
{
    public function getFormField($name = null, $title = null, $value = null)
    {
        $title = $title ?: 'Bar';
        return new TextField($name, $title, $value);
    }

    public function setFieldMapping($fieldMapping)
    {
    }
}
