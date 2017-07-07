<?php

/**
 * An extension to the {@link Form} class which provides the method
 * {@link enableSpamProtection()} helper.
 *
 * @package spamprotection
 */

class FormSpamProtectionExtension extends Extension
{
    /**
     * @config
     *
     * The default spam protector class name to use. Class should implement the
     * {@link SpamProtector} interface.
     *
     * @var string $spam_protector
     */
    private static $default_spam_protector;

    /**
     * @config
     *
     * The {@link enableSpamProtection} method will define which of the form
     * values correlates to this form mapped fields list. Totally custom forms
     * and subclassed SpamProtector instances are define their own mapping
     *
     * @var array $mappable_fields
     */
    private static $mappable_fields =  array(
        'id',
        'title',
        'body',
        'contextUrl',
        'contextTitle',
        'authorName',
        'authorMail',
        'authorUrl',
        'authorIp',
        'authorId'
    );
    
    /**
     * Instantiate a SpamProtector instance
     *
     * @param array $options Configuration options
     * @return SpamProtector
     */
    public static function get_protector($options = null)
    {
        // generate the spam protector
        if (isset($options['protector'])) {
            $protector = $options['protector'];
        } else {
            $protector = Config::inst()->get('FormSpamProtectionExtension', 'default_spam_protector');
        }

        if ($protector && class_exists($protector)) {
            return Injector::inst()->create($protector);
        } else {
            return null;
        }
    }

    /**
     * Activates the spam protection module.
     *
     * @param array $options
     */
    public function enableSpamProtection($options = array())
    {
        
        // captcha form field name (must be unique)
        if (isset($options['name'])) {
            $name = $options['name'];
        } else {
            $name = 'Captcha';
        }

        // captcha field title
        if (isset($options['title'])) {
            $title = $options['title'];
        } else {
            $title = '';
        }

        // set custom mapping on this form
        $protector = self::get_protector($options);

        if (isset($options['mapping'])) {
            $protector->setFieldMapping($options['mapping']);
        }

        if ($protector) {
            // add the form field
            if ($field = $protector->getFormField($name, $title)) {
                $field->setForm($this->owner);
                
                // Add before field specified by insertBefore
                $inserted = false;
                if (!empty($options['insertBefore'])) {
                    $inserted = $this->owner->Fields()->insertBefore($field, $options['insertBefore']);
                }
                if (!$inserted) {
                    // Add field to end if not added already
                    $this->owner->Fields()->push($field);
                }
            }
        }
    
        return $this->owner;
    }
}
