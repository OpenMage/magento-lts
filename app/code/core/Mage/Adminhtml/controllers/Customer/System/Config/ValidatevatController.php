<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * VAT validation controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Customer_System_Config_ValidatevatController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'system/config';

    /**
     * Perform customer VAT ID validation
     *
     * @return Varien_Object
     */
    protected function _validate()
    {
        return Mage::helper('customer')->checkVatNumber(
            $this->getRequest()->getParam('country'),
            $this->getRequest()->getParam('vat'),
        );
    }

    /**
     * Check whether vat is valid
     */
    public function validateAction()
    {
        $result = $this->_validate();
        $this->getResponse()->setBody((int) $result->getIsValid());
    }

    /**
     * Retrieve validation result as JSON
     */
    public function validateAdvancedAction()
    {
        /** @var Mage_Core_Helper_Data $coreHelper */
        $coreHelper = Mage::helper('core');

        $result = $this->_validate();
        $valid = $result->getIsValid();
        $success = $result->getRequestSuccess();
        // ID of the store where order is placed
        $storeId = $this->getRequest()->getParam('store_id');
        // Sanitize value if needed
        if (!is_null($storeId)) {
            $storeId = (int) $storeId;
        }

        $groupId = Mage::helper('customer')->getCustomerGroupIdBasedOnVatNumber(
            $this->getRequest()->getParam('country'),
            $result,
            $storeId,
        );

        $body = $coreHelper->jsonEncode([
            'valid' => $valid,
            'group' => $groupId,
            'success' => $success,
        ]);
        $this->getResponse()->setBody($body);
    }
}
