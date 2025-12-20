<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

/**
 * Class Mage_Admin_Model_Block
 *
 * @package    Mage_Adminhtml
 *
 * @method Mage_Admin_Model_Resource_Block            _getResource()
 * @method string                                     getBlockName()
 * @method Mage_Admin_Model_Resource_Block_Collection getCollection()
 * @method string                                     getIsAllowed()
 * @method Mage_Admin_Model_Resource_Block            getResource()
 * @method Mage_Admin_Model_Resource_Block_Collection getResourceCollection()
 */
class Mage_Admin_Model_Block extends Mage_Core_Model_Abstract
{
    public const BLOCK_NAME_REGEX = '/^[-_a-zA-Z0-9]+\/[-_a-zA-Z0-9\/]+$/';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('admin/block');
    }

    /**
     * @return array|true
     * @throws Exception
     */
    public function validate()
    {
        $validator  = $this->getValidationHelper();
        $violations = new ArrayObject();

        $blockName  = $this->getBlockName();

        $violations->append($validator->validateNotEmpty(
            value: $blockName,
            message: Mage::helper('adminhtml')->__('Block Name is required field.'),
        ));

        $violations->append($validator->validateChoice(
            value: $blockName,
            choices: Mage::helper('admin/block')->getDisallowedBlockNames(),
            message: Mage::helper('adminhtml')->__('Block Name is disallowed.'),
            match: false,
        ));

        $violations->append($validator->validateRegex(
            value: $blockName,
            pattern: self::BLOCK_NAME_REGEX,
            message: Mage::helper('adminhtml')->__('Block Name is incorrect.'),
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
     * Check is block with such type allowed for parsing via blockDirective method
     *
     * @param  string $type
     * @return bool
     */
    public function isTypeAllowed($type)
    {
        return Mage::helper('admin/block')->isTypeAllowed($type);
    }
}
