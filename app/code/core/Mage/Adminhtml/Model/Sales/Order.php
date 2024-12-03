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
 * Order control model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_Sales_Order
{
    /**
     * Retrieve adminhtml session singleton
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    public function checkRelation(Mage_Sales_Model_Order $order)
    {
        /**
         * Check customer existing
         */
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        if (!$customer->getId()) {
            $this->_getSession()->addNotice(
                Mage::helper('adminhtml')->__(' The customer does not exist in the system anymore.')
            );
        }

        /**
         * Check Item products existing
         */
        $productIds = [];
        foreach ($order->getAllItems() as $item) {
            $productIds[] = $item->getProductId();
        }

        $productCollection = Mage::getModel('catalog/product')->getCollection()
            ->addIdFilter($productIds)
            ->load();

        $hasBadItems = false;
        foreach ($order->getAllItems() as $item) {
            if (!$productCollection->getItemById($item->getProductId())) {
                $this->_getSession()->addError(
                    Mage::helper('adminhtml')->__(
                        'The item %s (SKU %s) does not exist in the catalog anymore.',
                        $item->getName(),
                        $item->getSku()
                    )
                );
                $hasBadItems = true;
            }
        }
        if ($hasBadItems) {
            $this->_getSession()->addError(
                Mage::helper('adminhtml')->__('Some of the ordered items do not exist in the catalog anymore and will be removed if you try to edit the order.')
            );
        }
        return $this;
    }
}
