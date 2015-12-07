<?php

/**
 * Editable Spam Protecter Field. Used with the User Defined Forms module (if
 * installed) to allow the user to have captcha fields with their custom forms
 *
 * @package spamprotection
 */
if (class_exists('EditableFormField')) {
    class EditableSpamProtectionField extends EditableFormField
    {
        private static $singular_name = 'Spam Protection Field';

        private static $plural_name = 'Spam Protection Fields';
        /**
         * Fields to include spam detection for
         *
         * @var array
         * @config
         */
        private static $check_fields = array(
            'EditableEmailField',
            'EditableTextField',
            'EditableNumericField'
        );

        public function getFormField()
        {
            // Get protector
            $protector = FormSpamProtectionExtension::get_protector();
            if (!$protector) {
                return false;
            }

            // Extract saved field mappings and update this field.
            $fieldMapping = array();
            foreach ($this->getCandidateFields() as $otherField) {
                $mapSetting = "Map-{$otherField->Name}";
                $spamField = $this->getSetting($mapSetting);
                $fieldMapping[$otherField->Name] = $spamField;
            }
            $protector->setFieldMapping($fieldMapping);

            // Generate field
            return $protector->getFormField($this->Name, $this->Title, null);
        }

        /**
         * Gets the list of all candidate spam detectable fields on this field's form
         *
         * @return DataList
         */
        protected function getCandidateFields()
        {

            // Get list of all configured classes available for spam detection
            $types = self::config()->check_fields;
            $typesInherit = array();
            foreach ($types as $type) {
                $subTypes = ClassInfo::subclassesFor($type);
                $typesInherit = array_merge($typesInherit, $subTypes);
            }

            // Get all candidates of the above types
            return $this
                ->Parent()
                ->Fields()
                ->filter('ClassName', $typesInherit)
                ->exclude('Title', ''); // Ignore this field and those without titles
        }

        public function getFieldConfiguration()
        {
            $fields = parent::getFieldConfiguration();

            // Get protector
            $protector = FormSpamProtectionExtension::get_protector();
            if (!$protector) {
                return $fields;
            }

            if ($this->Parent()->Fields() instanceof UnsavedRelationList) {
                return $fields;
            }

            // Each other text field in this group can be assigned a field mapping
            $mapGroup = FieldGroup::create(_t(
                'EditableSpamProtectionField.SPAMFIELDMAPPING',
                'Spam Field Mapping'
            ))->setDescription(_t(
                'EditableSpamProtectionField.SPAMFIELDMAPPINGDESCRIPTION',
                'Select the form fields that correspond to any relevant spam protection identifiers'
            ));

            // Generate field specific settings
            $mappableFields = Config::inst()->get('FormSpamProtectionExtension', 'mappable_fields');
            $mappableFieldsMerged = array_combine($mappableFields, $mappableFields);
            foreach ($this->getCandidateFields() as $otherField) {
                $mapSetting = "Map-{$otherField->Name}";
                $fieldOption = DropdownField::create(
                    $this->getSettingName($mapSetting),
                    $otherField->Title,
                    $mappableFieldsMerged,
                    $this->getSetting($mapSetting)
                )->setEmptyString('');
                $mapGroup->push($fieldOption);
            }
            $fields->insertBefore($mapGroup, $this->getSettingName('ExtraClass'));

            return $fields;
        }

        public function validateField($data, $form)
        {
            // In case you dont have this function in your php - primitive version
            if (!function_exists("array_column")) {
                function array_column($array, $column_name) {
                    return array_map(function ($element) use ($column_name) {
                        return $element[$column_name];
                    }, $array);
                }
            }
            if (!$formField->validate($form->getValidator())) {
                $errorArray = $form->getValidator()->getErrors();
                $errorText = $errorArray[array_search($this->Name, array_column($errorArray, 'fieldName'))]['message'];
                $form->addErrorMessage($this->Name, $errorText, 'error', false);
            }
        }

        public function getFieldValidationOptions()
        {
            return new FieldList();
        }

        public function getRequired()
        {
            return false;
        }

        public function getIcon()
        {
            return 'spamprotection/images/' . strtolower($this->class) . '.png';
        }

        public function showInReports()
        {
            return false;
        }
    }
}
