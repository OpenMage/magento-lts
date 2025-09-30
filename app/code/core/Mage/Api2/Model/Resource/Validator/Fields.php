<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * API2 Fields Validator
 *
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Resource_Validator_Fields extends Mage_Api2_Model_Resource_Validator
{
    /**
     * Config node key of current validator
     */
    public const CONFIG_NODE_KEY = 'fields';

    /**
     * Resource
     *
     * @var Mage_Api2_Model_Resource
     */
    protected $_resource;

    /**
     * List of Validators (Zend_Validate_Interface)
     * The key is a field name, a value is validator for this field
     *
     * @var array
     */
    protected $_validators;

    /**
     * List of required fields
     *
     * @var array
     */
    protected $_requiredFields = [];

    /**
     * Construct. Set all depends.
     *
     * Required parameteres for options:
     * - resource
     *
     * @param array $options
     * @throws Exception If passed parameter 'resource' is wrong
     */
    public function __construct($options)
    {
        if (!isset($options['resource']) || !$options['resource'] instanceof Mage_Api2_Model_Resource) {
            throw new Exception("Passed parameter 'resource' is wrong.");
        }
        $this->_resource = $options['resource'];

        $validationConfig = $this->_resource->getConfig()->getValidationConfig(
            $this->_resource->getResourceType(),
            self::CONFIG_NODE_KEY,
        );
        if (!is_array($validationConfig)) {
            $validationConfig = [];
        }
        $this->_buildValidatorsChain($validationConfig);
    }

    /**
     * Build validator chain with config data
     *
     * @throws Exception If validator type is not set
     * @throws Exception If validator is not exist
     */
    protected function _buildValidatorsChain(array $validationConfig)
    {
        foreach ($validationConfig as $field => $validatorsConfig) {
            if (count($validatorsConfig)) {
                $chainForOneField = new Zend_Validate();
                foreach ($validatorsConfig as $validatorName => $validatorConfig) {
                    // it is required field
                    if ($validatorName == 'required' && $validatorConfig == 1) {
                        $this->_requiredFields[] = $field;
                        continue;
                    }
                    // instantiation of the validator class
                    if (!isset($validatorConfig['type'])) {
                        throw new Exception("Validator type is not set for {$validatorName}");
                    }
                    $validator = $this->_getValidatorInstance(
                        $validatorConfig['type'],
                        !empty($validatorConfig['options']) ? $validatorConfig['options'] : [],
                    );
                    // set custom message
                    if (isset($validatorConfig['message'])) {
                        $validator->setMessage($validatorConfig['message']);
                    }
                    // add to list of validators
                    $chainForOneField->addValidator($validator);
                }
                $this->_validators[$field] = $chainForOneField;
            }
        }
    }

    /**
     * Get validator object instance
     * Override the method if we need to use not only Zend validators!
     *
     * @param string $type
     * @param array $options
     * @return Zend_Validate_Interface
     * @throws Exception If validator is not exist
     */
    protected function _getValidatorInstance($type, $options)
    {
        $validatorClass = 'Zend_Validate_' . $type;
        if (!class_exists($validatorClass)) {
            throw new Exception("Validator {$type} is not exist");
        }
        return new $validatorClass($options);
    }

    /**
     * Validate data.
     * If fails validation, then this method returns false, and
     * getErrors() will return an array of errors that explain why the
     * validation failed.
     *
     * @param bool $isPartial
     * @return bool
     */
    public function isValidData(array $data, $isPartial = false)
    {
        $isValid = true;

        // required fields
        if (!$isPartial && count($this->_requiredFields) > 0) {
            $notEmptyValidator = new Zend_Validate_NotEmpty();
            foreach ($this->_requiredFields as $requiredField) {
                if (!$notEmptyValidator->isValid($data[$requiredField] ?? null)) {
                    $isValid = false;
                    foreach ($notEmptyValidator->getMessages() as $message) {
                        $this->_addError(sprintf('%s: %s', $requiredField, $message));
                    }
                }
            }
        }

        // fields rules
        foreach ($data as $field => $value) {
            if (isset($this->_validators[$field])) {
                /** @var Zend_Validate_Interface $validator */
                $validator = $this->_validators[$field];
                if (!$validator->isValid($value)) {
                    $isValid = false;
                    foreach ($validator->getMessages() as $message) {
                        $this->_addError(sprintf('%s: %s', $field, $message));
                    }
                }
            }
        }

        return $isValid;
    }
}
