<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_Admin_Model_Variable
 *
 * @method Mage_Admin_Model_Resource_Variable _getResource()
 * @method Mage_Admin_Model_Resource_Variable getResource()
 * @method Mage_Admin_Model_Resource_Variable_Collection getCollection()
 *
 * @method string getIsAllowed()
 * @method string getVariableName()
 */
class Mage_Admin_Model_Variable extends Mage_Core_Model_Abstract
{
    /**
     * Initialize variable model
     */
    protected function _construct()
    {
        $this->_init('admin/variable');
    }

    /**
     * @return array|bool
     * @throws Exception
     * @throws Zend_Validate_Exception
     */
    public function validate()
    {
        $errors = [];

        if (!Zend_Validate::is($this->getVariableName(), 'NotEmpty')) {
            $errors[] = Mage::helper('adminhtml')->__('Variable Name is required field.');
        }
        if (!Zend_Validate::is($this->getVariableName(), 'Regex', ['/^[-_a-zA-Z0-9\/]*$/'])) {
            $errors[] = Mage::helper('adminhtml')->__('Variable Name is incorrect.');
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
     * Check is config directive with given path can be parsed via configDirective method
     *
     * @param string $path
     * @return bool
     */
    public function isPathAllowed($path)
    {
        return Mage::helper('admin/variable')->isPathAllowed($path);
    }
}
