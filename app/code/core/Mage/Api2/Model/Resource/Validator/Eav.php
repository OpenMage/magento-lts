<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * API2 EAV Validator
 *
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Resource_Validator_Eav extends Mage_Api2_Model_Resource_Validator
{
    /**
     * Config node key of current validator
     */
    public const CONFIG_NODE_KEY = 'eav';

    /**
     * Form path
     *
     * @var string
     */
    protected $_formPath;

    /**
     * Entity model
     *
     * @var Mage_Core_Model_Abstract
     */
    protected $_entity;

    /**
     * Form code
     *
     * @var string
     */
    protected $_formCode;

    /**
     * Eav form model
     *
     * @var Mage_Eav_Model_Form
     */
    protected $_eavForm;

    /**
     * Construct. Set all depends.
     *
     * Required parameteres for options:
     * - resource
     *
     * @param array $options
     * @throws Exception If passed parameter 'resource' is wrong
     * @throws Exception If config parameter 'formPath' is empty
     * @throws Exception If config parameter 'formCode' is empty
     * @throws Exception If config parameter 'entity' is wrong
     * @throws Exception If entity is not model
     * @throws Exception If eav form is not found
     */
    public function __construct($options)
    {
        if (!isset($options['resource']) || !$options['resource'] instanceof Mage_Api2_Model_Resource) {
            throw new Exception("Passed parameter 'resource' is wrong.");
        }
        $resource = $options['resource'];
        $userType = $resource->getUserType();

        $validationConfig = $resource->getConfig()->getValidationConfig(
            $resource->getResourceType(),
            self::CONFIG_NODE_KEY,
        );

        if (empty($validationConfig[$userType]['form_model'])) {
            throw new Exception("Config parameter 'formPath' is empty.");
        }
        $this->_formPath = $validationConfig[$userType]['form_model'];

        if (empty($validationConfig[$userType]['form_code'])) {
            throw new Exception("Config parameter 'formCode' is empty.");
        }
        $this->_formCode = $validationConfig[$userType]['form_code'];

        if (empty($validationConfig[$userType]['entity_model'])) {
            throw new Exception("Config parameter 'entity' is wrong.");
        }

        $entityModel = Mage::getModel($validationConfig[$userType]['entity_model']);
        if (empty($entityModel) || !$entityModel instanceof Mage_Core_Model_Abstract) {
            throw new Exception('Entity is not model.');
        }
        $this->_entity = $entityModel;

        $formModel = Mage::getModel($this->_formPath);
        if (empty($formModel) || !$formModel instanceof Mage_Eav_Model_Form) {
            throw new Exception("Eav form '{$formModel}' is not found.");
        }
        $this->_eavForm = $formModel;

        $this->_eavForm->setEntity($this->_entity)
            ->setFormCode($this->_formCode)
            ->ignoreInvisible(false);
    }

    /**
     * Validate attribute value for attributes with source models
     *
     * @param mixed $attrValue
     * @return array|bool
     */
    protected function _validateAttributeWithSource(Mage_Eav_Model_Entity_Attribute_Abstract $attribute, $attrValue)
    {
        $errors = [];

        // validate attributes with source models
        if ($attrValue !== null && $attribute->getSourceModel()) {
            if ($attribute->getFrontendInput() !== 'multiselect' && is_array($attrValue)) {
                return ['Invalid value type for ' . $attribute->getAttributeCode()];
            }
            $possibleValues = $attribute->getSource()->getAllOptions(false);

            foreach ((array) $attrValue as $value) {
                if (is_scalar($value)) {
                    $value = (string) $value;
                    $isValid = false;
                    foreach ($possibleValues as $optionData) {
                        // comparison without types check is performed only when both values are numeric
                        $useStrictMode = !(is_numeric($value) && is_numeric($optionData['value']));
                        $isValid = $useStrictMode ? $value === $optionData['value'] : $value == $optionData['value'];
                        if ($isValid) {
                            break;
                        }
                    }
                    if (!$isValid) {
                        $errors[] = 'Invalid value "' . $value . '" for ' . $attribute->getAttributeCode();
                    }
                } else {
                    $errors[] = 'Invalid value type for ' . $attribute->getAttributeCode();
                }
            }
        }
        return $errors ? $errors : true;
    }

    /**
     * Filter request data.
     *
     * @return array Filtered data
     */
    public function filter(array $data)
    {
        return array_intersect_key($this->_eavForm->extractData($this->_eavForm->prepareRequest($data)), $data);
    }

    /**
     * Validate entity.
     * If the $partial parameter is TRUE, then we validate only those parameters that were passed.
     *
     * If fails validation, then this method returns false, and
     * getErrors() will return an array of errors that explain why the
     * validation failed.
     *
     * @param bool $partial
     * @return bool
     */
    public function isValidData(array $data, $partial = false)
    {
        $errors = [];
        foreach ($this->_eavForm->getAttributes() as $attribute) {
            if ($partial && !array_key_exists($attribute->getAttributeCode(), $data)) {
                continue;
            }
            if ($this->_eavForm->ignoreInvisible() && !$attribute->getIsVisible()) {
                continue;
            }
            $attrValue = $data[$attribute->getAttributeCode()] ?? null;

            $result = Mage_Eav_Model_Attribute_Data::factory($attribute, $this->_eavForm->getEntity())
                ->setExtractedData($data)
                ->validateValue($attrValue);

            if ($result !== true) {
                $errors = array_merge($errors, $result);
            } else {
                $result = $this->_validateAttributeWithSource($attribute, $attrValue);

                if ($result !== true) {
                    $errors = array_merge($errors, $result);
                }
            }
        }
        $this->_setErrors($errors);

        return $errors ? false : true;
    }

    /**
     * Returns an array of errors
     *
     * @return array
     */
    public function getErrors()
    {
        // business asked to avoid additional validation message, so we filter it here
        $errors        = [];
        $requiredAttrs = [];
        $isRequiredRE  = '/^' . str_replace(
            '%s',
            '(.+)',
            preg_quote(Mage::helper('eav')->__('"%s" is a required value.'), '/'),
        ) . '$/';
        $greaterThanRE = '/^' . str_replace(
            '%s',
            '(.+)',
            preg_quote(Mage::helper('eav')->__('"%s" length must be equal or greater than %s characters.'), '/'),
        ) . '$/';

        // find all required attributes labels
        foreach ($this->_errors as $error) {
            if (preg_match($isRequiredRE, $error, $matches)) {
                $requiredAttrs[$matches[1]] = true;
            }
        }
        // exclude additional messages for required attributes been failed
        foreach ($this->_errors as $error) {
            if (preg_match($isRequiredRE, $error)
                || !preg_match($greaterThanRE, $error, $matches)
                || !isset($requiredAttrs[$matches[1]])
            ) {
                $errors[] = $error;
            }
        }
        return $errors;
    }
}
