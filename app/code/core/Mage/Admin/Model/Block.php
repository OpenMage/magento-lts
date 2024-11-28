<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_Admin_Model_Block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 *
 * @method Mage_Admin_Model_Resource_Block _getResource()
 * @method Mage_Admin_Model_Resource_Block getResource()
 * @method Mage_Admin_Model_Resource_Block_Collection getCollection()
 *
 * @method string getBlockName()
 * @method string getIsAllowed()
 */
class Mage_Admin_Model_Block extends Mage_Core_Model_Abstract
{
    /**
     * Initialize variable model
     */
    protected function _construct()
    {
        $this->_init('admin/block');
    }

    /**
     * @return array|true
     * @throws Exception
     * @throws Zend_Validate_Exception
     */
    public function validate()
    {
        $errors = [];

        if (!Zend_Validate::is($this->getBlockName(), 'NotEmpty')) {
            $errors[] = Mage::helper('adminhtml')->__('Block Name is required field.');
        }
        $disallowedBlockNames = Mage::helper('admin/block')->getDisallowedBlockNames();
        if (in_array($this->getBlockName(), $disallowedBlockNames)) {
            $errors[] = Mage::helper('adminhtml')->__('Block Name is disallowed.');
        }
        if (!Zend_Validate::is($this->getBlockName(), 'Regex', ['/^[-_a-zA-Z0-9]+\/[-_a-zA-Z0-9\/]+$/'])) {
            $errors[] = Mage::helper('adminhtml')->__('Block Name is incorrect.');
        }

        if (!in_array($this->getIsAllowed(), ['0', '1'])) {
            $errors[] = Mage::helper('adminhtml')->__('Is Allowed is required field.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Check is block with such type allowed for parsing via blockDirective method
     *
     * @param string $type
     * @return bool
     */
    public function isTypeAllowed($type)
    {
        return Mage::helper('admin/block')->isTypeAllowed($type);
    }
}
