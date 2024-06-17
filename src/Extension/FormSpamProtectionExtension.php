<?php

namespace SilverStripe\SpamProtection\Extension;

use LogicException;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\Form;

/**
 * An extension to the {@link Form} class which provides the method
 * {@link enableSpamProtection()} helper.
 *
 * @extends Extension<Form>
 */
class FormSpamProtectionExtension extends Extension
{
    use Configurable;

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
    private static $mappable_fields =  [
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
    ];

    /**
     * @config
     *
     * The field name to use for the {@link SpamProtector} {@link FormField}
     *
     * @var string $spam_protector
     */
    private static $field_name = "Captcha";

    /**
     * Instantiate a SpamProtector instance
     *
     * @param array $options Configuration options
     * @return SpamProtector|null
     */
    public static function get_protector($options = null)
    {
        // generate the spam protector
        if (isset($options['protector'])) {
            $protector = $options['protector'];
        } else {
            $protector = static::config()->get('default_spam_protector');
        }

        if ($protector && class_exists($protector ?? '')) {
            return Injector::inst()->create($protector);
        } else {
            return null;
        }
    }

    /**
     * Activates the spam protection module.
     *
     * @param array $options
     * @throws LogicException when get_protector method returns NULL.
     * @return Form
     */
    public function enableSpamProtection($options = [])
    {

        // captcha form field name (must be unique)
        if (isset($options['name'])) {
            $name = $options['name'];
        } else {
            $name = $this->config()->get('field_name');
        }

        // captcha field title
        if (isset($options['title'])) {
            $title = $options['title'];
        } else {
            $title = '';
        }

        // set custom mapping on this form
        $protector = FormSpamProtectionExtension::get_protector($options);

        if ($protector === null) {
            throw new LogicException('No spam protector has been set. Null is not valid value.');
        }

        if ($protector && isset($options['mapping'])) {
            $protector->setFieldMapping($options['mapping']);
        }

        if ($protector) {
            // add the form field
            if ($field = $protector->getFormField($name, $title)) {
                $field->setForm($this->owner);

                // Add before field specified by insertBefore
                $inserted = false;
                if (!empty($options['insertBefore'])) {
                    $inserted = $this->owner->Fields()->insertBefore($options['insertBefore'], $field);
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
