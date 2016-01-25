<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * VAT validation controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Customer_System_Config_ValidatevatController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Perform customer VAT ID validation
     *
     * @return Varien_Object
     */
    protected function _validate()
    {
        return Mage::helper('customer')->checkVatNumber(
            $this->getRequest()->getParam('country'),
            $this->getRequest()->getParam('vat')
        );
    }

    /**
     * Check whether vat is valid
     *
     * @return void
     */
    public function validateAction()
    {
        $result = $this->_validate();
        $this->getResponse()->setBody((int)$result->getIsValid());
    }

    /**
     * Retrieve validation result as JSON
     *
     * @return void
     */
    public function validateAdvancedAction()
    {
        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = Mage::helper('core');

        $result = $this->_validate();
        $valid = $result->getIsValid();
        $success = $result->getRequestSuccess();
        // ID of the store where order is placed
        $storeId = $this->getRequest()->getParam('store_id');
        // Sanitize value if needed
        if (!is_null($storeId)) {
            $storeId = (int)$storeId;
        }

        $groupId = Mage::helper('customer')->getCustomerGroupIdBasedOnVatNumber(
            $this->getRequest()->getParam('country'), $result, $storeId
        );

        $body = $coreHelper->jsonEncode(array(
            'valid' => $valid,
            'group' => $groupId,
            'success' => $success
        ));
        $this->getResponse()->setBody($body);
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config');
    }
}
