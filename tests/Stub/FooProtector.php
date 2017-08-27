<?php

namespace SilverStripe\SpamProtection\Tests\Stub;

use SilverStripe\Dev\TestOnly;
use SilverStripe\Forms\TextField;
use SilverStripe\SpamProtection\SpamProtector;

/**
 * @package spamprotection
 */
class FooProtector implements SpamProtector, TestOnly
{
    public function getFormField($name = null, $title = null, $value = null)
    {
        return new TextField($name, 'Foo', $value);
    }

    public function setFieldMapping($fieldMapping)
    {
    }
}
