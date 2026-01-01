<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * API2 Abstarct Validator
 *
 * This is an object to which we encapsulate all business logic of validation and different invariants.
 * But instead of different validators, we group all logic in one class but in different methods.
 *
 * If fails validation, then validation method returns false, and
 * getErrors() will return an array of errors that explain why the
 * validation failed.
 *
 * @package    Mage_Api2
 */
abstract class Mage_Api2_Model_Resource_Validator
{
    /**
     * Array of validation failure errors.
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * Set an array of errors
     *
     * @return Mage_Api2_Model_Resource_Validator
     */
    protected function _setErrors(array $data)
    {
        $this->_errors = array_values($data);
        return $this;
    }

    /**
     * Add errors
     *
     * @param  array                              $errors
     * @return Mage_Api2_Model_Resource_Validator
     */
    protected function _addErrors($errors)
    {
        foreach ($errors as $error) {
            $this->_addError($error);
        }

        return $this;
    }

    /**
     * Add error
     *
     * @param  string                             $error
     * @return Mage_Api2_Model_Resource_Validator
     */
    protected function _addError($error)
    {
        $this->_errors[] = $error;
        return $this;
    }

    /**
     * Returns an array of errors that explain why the most recent isValidData()
     * call returned false. The array keys are validation failure error identifiers,
     * and the array values are the corresponding human-readable error strings.
     *
     * If isValidData() was never called or if the most recent isValidData() call
     * returned true, then this method returns an empty array.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }
}
