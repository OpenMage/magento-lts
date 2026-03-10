<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

use Symfony\Component\Validator\Constraint;

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
     * List of Validators (Constraint[])
     * The key is a field name, a value is validator for this field
     *
     * @var array<string, ArrayObject>
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
     * @param  array     $options
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
     * @throws Mage_Core_Exception If validator type is not set
     */
    protected function _buildValidatorsChain(array $validationConfig)
    {
        $validator = $this->getValidationHelper();

        foreach ($validationConfig as $field => $validatorsConfig) {
            $field = (string) $field;
            if (count($validatorsConfig)) {
                $chainForOneField = new ArrayObject();
                foreach ($validatorsConfig as $validatorName => $validatorConfig) {
                    // it is required field
                    if ($validatorName == 'required' && $validatorConfig == 1) {
                        $this->_requiredFields[] = $field;
                        continue;
                    }

                    // instantiation of the validator class
                    if (!isset($validatorConfig['type'])) {
                        throw new Mage_Core_Exception("Validator type is not set for $validatorName");
                    }

                    $options = $validatorConfig['options'] ?? [];

                    // set custom message
                    if (isset($validatorConfig['message'])) {
                        $options['message'] = $validatorConfig['message'];
                    }

                    // add to list of validators
                    $constraints = $validator->getContraintsByType(type: $validatorConfig['type'], options: $options);
                    $chainForOneField->append($constraints);
                }

                $this->_validators[$field] = $chainForOneField;
            }
        }
    }

    /**
     * Validate data.
     * If fails validation, then this method returns false, and
     * getErrors() will return an array of errors that explain why the
     * validation failed.
     *
     * @param  bool $isPartial
     * @return bool
     */
    public function isValidData(array $data, $isPartial = false)
    {
        $validator = $this->getValidationHelper();

        $isValid = true;

        // required fields
        if (!$isPartial && count($this->_requiredFields) > 0) {
            foreach ($this->_requiredFields as $requiredField) {
                $violations = $validator->validateNotEmpty(value: $data[$requiredField] ?? null);
                if ($violations->count() > 0) {
                    $isValid = false;
                    foreach ($violations as $violation) {
                        $this->_addError(sprintf('%s: %s', $requiredField, $violation->getMessage()));
                    }
                }
            }
        }

        // fields rules
        foreach ($data as $field => $value) {
            if (isset($this->_validators[$field])) {
                /** @var ArrayObject $chainForOneField */
                foreach ($this->_validators[$field] as $chainForOneField) {
                    /** @var Constraint[] $constraints */
                    foreach ($chainForOneField as $constraints) {
                        $violations = $validator->validate(value: $value, constraints: $constraints);
                        if ($violations->count() > 0) {
                            $isValid = false;
                            foreach ($violations as $violation) {
                                $this->_addError(sprintf('%s: %s', $field, $violation->getMessage()));
                            }
                        }
                    }
                }
            }
        }

        return $isValid;
    }

    protected function getValidationHelper(): Mage_Core_Helper_Validate
    {
        /** @var Mage_Core_Helper_Validate $validator */
        $validator = Mage::helper('core/validate');
        return $validator;
    }
}
