<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

/**
 * Class Mage_Admin_Model_Variable
 *
 * @package    Mage_Admin
 *
 * @method Mage_Admin_Model_Resource_Variable            _getResource()
 * @method Mage_Admin_Model_Resource_Variable_Collection getCollection()
 * @method string                                        getIsAllowed()
 * @method Mage_Admin_Model_Resource_Variable            getResource()
 * @method Mage_Admin_Model_Resource_Variable_Collection getResourceCollection()
 * @method string                                        getVariableName()
 */
class Mage_Admin_Model_Variable extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('admin/variable');
    }

    /**
     * @return array|true
     * @throws Exception
     */
    public function validate()
    {
        $validator  = $this->getValidationHelper();
        $violations = new ArrayObject();

        $variableName = $this->getVariableName();

        $violations->append($validator->validateNotEmpty(
            value: $variableName,
            message: Mage::helper('adminhtml')->__('Variable Name is required field.'),
        ));

        $violations->append($validator->validateRegex(
            value: $variableName,
            pattern: '/^[-_a-zA-Z0-9\/]*$/',
            message: Mage::helper('adminhtml')->__('Variable Name is incorrect.'),
        ));

        $violations->append($validator->validateChoice(
            value: $this->getIsAllowed(),
            choices: ['0', '1'],
            message: Mage::helper('adminhtml')->__('Is Allowed is required field.'),
        ));

        $errors = $validator->getErrorMessages($violations);
        if (!$errors) {
            return true;
        }

        return (array) $errors;
    }

    /**
     * Check is config directive with given path can be parsed via configDirective method
     *
     * @param  string $path
     * @return bool
     */
    public function isPathAllowed($path)
    {
        return Mage::helper('admin/variable')->isPathAllowed($path);
    }
}
