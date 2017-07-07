<?php

/**
 * @package spamprotection
 *
 * @deprecated 1.0
 */

class SpamProtectorManager
{
    private static $spam_protector = null;

    public static function set_spam_protector($protector)
    {
        Deprecation::notice(
            '1.1',
            'SpamProtectorManager::set_spam_protector() is deprecated. '.
            'Use the new config system. FormSpamProtectorExtension.default_spam_protector'
        );

        self::$spam_protector = $protector;
    }

    public static function get_spam_protector()
    {
        Deprecation::notice(
            '1.1',
            'SpamProtectorManager::get_spam_protector() is deprecated'.
            'Use the new config system. FormSpamProtectorExtension.default_spam_protector'
        );

        return self::$spam_protector;
    }
    
    public static function update_form($form, $before = null, $fieldsToSpamServiceMapping = array(), $title = null, $rightTitle = null)
    {
        Deprecation::notice(
            '1.1',
            'SpamProtectorManager::update_form is deprecated'.
            'Please use $form->enableSpamProtection() for adding spamprotection'
        );

        return $form->enableSpamProtection();
    }
}
