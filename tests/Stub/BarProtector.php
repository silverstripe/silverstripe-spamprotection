<?php

namespace SilverStripe\SpamProtection\Tests\Stub;

use SilverStripe\Dev\TestOnly;
use SilverStripe\Forms\TextField;
use SilverStripe\SpamProtection\SpamProtector;

/**
 * @package spamprotection
 */
class BarProtector implements SpamProtector, TestOnly
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
