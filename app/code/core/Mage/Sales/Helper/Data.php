<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales module base helper
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Maximum available number
     */
    public const MAXIMUM_AVAILABLE_NUMBER = 99999999;

    /**
     * Default precision for price calculations
     */
    public const PRECISION_VALUE = 0.0001;

    protected $_moduleName = 'Mage_Sales';

    /**
     * Check quote amount
     *
     * @param float $amount
     * @return $this
     */
    public function checkQuoteAmount(Mage_Sales_Model_Quote $quote, $amount)
    {
        if (!$quote->getHasError() && ($amount >= self::MAXIMUM_AVAILABLE_NUMBER)) {
            $quote->setHasError(true);
            $quote->addMessage(
                $this->__('Items maximum quantity or price do not allow checkout.'),
            );
        }
        return $this;
    }

    /**
     * Check allow to send new order confirmation email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendNewOrderConfirmationEmail($store = null)
    {
        return Mage::getStoreConfigFlag(Mage_Sales_Model_Order::XML_PATH_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send new order email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendNewOrderEmail($store = null)
    {
        return $this->canSendNewOrderConfirmationEmail($store);
    }

    /**
     * Check allow to send order comment email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendOrderCommentEmail($store = null)
    {
        return Mage::getStoreConfigFlag(Mage_Sales_Model_Order::XML_PATH_UPDATE_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send new shipment email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendNewShipmentEmail($store = null)
    {
        return Mage::getStoreConfigFlag(Mage_Sales_Model_Order_Shipment::XML_PATH_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send shipment comment email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendShipmentCommentEmail($store = null)
    {
        return Mage::getStoreConfigFlag(Mage_Sales_Model_Order_Shipment::XML_PATH_UPDATE_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send new invoice email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendNewInvoiceEmail($store = null)
    {
        return Mage::getStoreConfigFlag(Mage_Sales_Model_Order_Invoice::XML_PATH_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send invoice comment email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendInvoiceCommentEmail($store = null)
    {
        return Mage::getStoreConfigFlag(Mage_Sales_Model_Order_Invoice::XML_PATH_UPDATE_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send new creditmemo email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendNewCreditmemoEmail($store = null)
    {
        return Mage::getStoreConfigFlag(Mage_Sales_Model_Order_Creditmemo::XML_PATH_EMAIL_ENABLED, $store);
    }

    /**
     * Check allow to send creditmemo comment email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendCreditmemoCommentEmail($store = null)
    {
        return Mage::getStoreConfigFlag(Mage_Sales_Model_Order_Creditmemo::XML_PATH_UPDATE_EMAIL_ENABLED, $store);
    }

    /**
     * Get old field map
     *
     * @param string $entityId
     * @return array
     */
    public function getOldFieldMap($entityId)
    {
        $node = Mage::getConfig()->getNode('global/sales/old_fields_map/' . $entityId);
        if ($node === false) {
            return [];
        }
        return (array) $node;
    }
}
