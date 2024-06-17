<?php

namespace SilverStripe\SpamProtection;

use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormField;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\UnsavedRelationList;
use SilverStripe\SpamProtection\Extension\FormSpamProtectionExtension;
use SilverStripe\UserForms\Model\EditableFormField;
use SilverStripe\UserForms\Model\EditableFormField\EditableEmailField;
use SilverStripe\UserForms\Model\EditableFormField\EditableNumericField;
use SilverStripe\UserForms\Model\EditableFormField\EditableTextField;

if (!class_exists(EditableFormField::class)) {
    return;
}

/**
 * Editable Spam Protecter Field. Used with the User Defined Forms module (if
 * installed) to allow the user to have captcha fields with their custom forms
 *
 * @package spamprotection
 */
class EditableSpamProtectionField extends EditableFormField
{
    private static $singular_name = 'Spam Protection Field';

    private static $plural_name = 'Spam Protection Fields';

    private static $table_name = 'EditableSpamProtectionField';

    /**
     * Fields to include spam detection for
     *
     * @var array
     * @config
     */
    private static $check_fields = [
        EditableEmailField::class,
        EditableTextField::class,
        EditableNumericField::class
    ];

    private static $db = [
        'SpamFieldSettings' => 'Text'
    ];

    /**
     * @var FormField
     */
    protected $formField = null;

    public function getFormField()
    {
        if ($this->formField) {
            return $this->formField;
        }

        // Get protector
        $protector = FormSpamProtectionExtension::get_protector();
        if (!$protector) {
            return false;
        }

        // Extract saved field mappings and update this field.
        $fieldMapping = [];
        foreach ($this->getCandidateFields() as $otherField) {
            $mapSetting = "Map-{$otherField->Name}";
            $spamField = $this->spamMapValue($mapSetting);
            $fieldMapping[$otherField->Name] = $spamField;
        }
        $protector->setFieldMapping($fieldMapping);

        // Generate field
        $field = $protector->getFormField($this->Name, $this->Title, null);

        $this->doUpdateFormField($field);

        return $field;
    }

    /**
     * @param FormField $field
     * @return EditableSpamProtectionField
     */
    public function setFormField(FormField $field)
    {
        $this->formField = $field;

        return $this;
    }

    /**
     * Gets the list of all candidate spam detectable fields on this field's form
     *
     * @return DataList<EditableFormField>
     */
    protected function getCandidateFields()
    {

        // Get list of all configured classes available for spam detection
        $types = $this->config()->get('check_fields');
        $typesInherit = [];
        foreach ($types as $type) {
            $subTypes = ClassInfo::subclassesFor($type);
            $typesInherit = array_merge($typesInherit, $subTypes);
        }

        // Get all candidates of the above types
        $parent = $this->Parent();
        if (!$parent) {
            return DataList::create(EditableFormField::class);
        }
        return $parent
            ->Fields()
            ->filter('ClassName', $typesInherit)
            ->exclude('Title', ''); // Ignore this field and those without titles
    }

    /**
     * Write the spam field mapping values to a serialised DB field
     *
     * {@inheritDoc}
     */
    public function onBeforeWrite()
    {
        $fieldMap = json_decode($this->SpamFieldSettings ?? '', true);
        if (empty($fieldMap)) {
            $fieldMap = [];
        }

        foreach ($this->record as $key => $value) {
            if (substr($key ?? '', 0, 8) === 'spammap-') {
                $fieldMap[substr($key, 8)] = $value;
            }
        }
        $this->setField('SpamFieldSettings', json_encode($fieldMap));

        return parent::onBeforeWrite();
    }

    /**
     * Used in userforms 3.x and above
     *
     * {@inheritDoc}
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // Get protector
        $protector = FormSpamProtectionExtension::get_protector();
        if (!$protector) {
            return $fields;
        }

        if ($this->Parent()->Fields() instanceof UnsavedRelationList) {
            return $fields;
        }

        // Each other text field in this group can be assigned a field mapping
        $mapGroup = FieldGroup::create()
            ->setTitle(_t(__CLASS__.'.SPAMFIELDMAPPING', 'Spam Field Mapping'))
            ->setName('SpamFieldMapping')
            ->setDescription(_t(
                __CLASS__.'.SPAMFIELDMAPPINGDESCRIPTION',
                'Select the form fields that correspond to any relevant spam protection identifiers'
            ));

        // Generate field specific settings
        $mappableFields = FormSpamProtectionExtension::config()->get('mappable_fields');
        $mappableFieldsMerged = array_combine($mappableFields ?? [], $mappableFields ?? []);
        foreach ($this->getCandidateFields() as $otherField) {
            $mapSetting = "Map-{$otherField->Name}";
            $fieldOption = DropdownField::create(
                'spammap-' . $mapSetting,
                $otherField->Title,
                $mappableFieldsMerged,
                $this->spamMapValue($mapSetting)
            )->setEmptyString('');
            $mapGroup->push($fieldOption);
        }
        $fields->addFieldToTab('Root.Main', $mapGroup);

        return $fields;
    }

    /**
     * Try to retrieve a value for the given spam field map name from the serialised data
     *
     * @param string $mapSetting
     * @return string
     */
    public function spamMapValue($mapSetting)
    {
        $map = json_decode($this->SpamFieldSettings ?? '', true);
        if (empty($map)) {
            $map = [];
        }

        if (array_key_exists($mapSetting, $map ?? [])) {
            return $map[$mapSetting];
        }
        return '';
    }

    /**
     * Using custom validateField method
     * as Spam Protection Field implementations may have their own error messages
     * and may not be based on the field being required, e.g. Honeypot Field
     *
     * @param array $data
     * @param Form $form
     * @return void
     */
    public function validateField($data, $form)
    {
        $formField = $this->getFormField();
        $formField->setForm($form);

        if (isset($data[$this->Name])) {
            $formField->setValue($data[$this->Name]);
        }

        $validator = $form->getValidator();
        if (!$formField->validate($validator)) {
            $errors = $validator->getErrors();
            $foundError = false;

            // field validate implementation may not add error to validator
            if (count($errors ?? []) > 0) {
                // check if error already added from fields' validate method
                foreach ($errors as $error) {
                    if ($error['fieldName'] == $this->Name) {
                        $foundError = $error;
                        break;
                    }
                }
            }

            if ($foundError !== false) {
                // use error messaging already set from validate method
                $form->sessionMessage($foundError['message'], $foundError['messageType']);
            } else {
                // fallback to custom message set in CMS or default message if none set
                $form->sessionError($this->getErrorMessage()->HTML());
            }
        }
    }

    public function getFieldValidationOptions()
    {
        return FieldList::create();
    }

    public function getRequired()
    {
        return false;
    }

    public function getIcon()
    {
        $resource = ModuleLoader::getModule('silverstripe/spamprotection')
            ->getResource('images/editablespamprotectionfield.png');

        if (!$resource->exists()) {
            return '';
        }

        return $resource->getURL();
    }

    public function showInReports()
    {
        return false;
    }
}
