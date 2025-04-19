<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax Event Observer
 *
 * @category   Mage
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Observer_SalesEventConvertQuoteAddressToOrder implements Mage_Core_Observer_Interface
{
    /**
     * Put quote address tax information into order
     */
    public function execute(Varien_Event_Observer $observer): self
    {
        /** @var Mage_Sales_Model_Quote_Address $address */
        $address = $observer->getEvent()->getDataByKey('address');
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getDataByKey('order');

        $taxes = $address->getAppliedTaxes();
        if (is_array($taxes)) {
            if (is_array($order->getAppliedTaxes())) {
                $taxes = array_merge($order->getAppliedTaxes(), $taxes);
            }
            $order->setAppliedTaxes($taxes);
            $order->setConvertingFromQuote(true);
        }

        return $this;
    }
}
