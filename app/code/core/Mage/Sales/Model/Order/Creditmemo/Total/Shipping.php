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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order creditmemo shipping total calculation model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Creditmemo_Total_Shipping extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        $allowedAmount      = $order->getShippingAmount()-$order->getShippingRefunded();
        $baseAllowedAmount  = $order->getBaseShippingAmount()-$order->getBaseShippingRefunded();

        /**
         * Check if shipping amount was specified (from invoice or another source)
         */
        $baseShippingAmount = $creditmemo->getBaseShippingAmount();
        if ($baseShippingAmount) {
            $baseShippingAmount = Mage::app()->getStore()->roundPrice($baseShippingAmount);
            if ($baseShippingAmount<$baseAllowedAmount) {
                $shippingAmount = $allowedAmount*$baseShippingAmount/$baseAllowedAmount;
                $shippingAmount = Mage::app()->getStore()->roundPrice($shippingAmount);
            } elseif ($baseShippingAmount==$baseAllowedAmount) {
                $shippingAmount = $allowedAmount;
            } else {
                $baseAllowedAmount = $order->formatBasePrice($baseAllowedAmount);
                Mage::throwException(
                    Mage::helper('sales')->__('Maximum shipping amount allowed to refound is: %s', $baseAllowedAmount)
                );
            }
        } else {
            $baseShippingAmount = $baseAllowedAmount;
            $shippingAmount     = $allowedAmount;
        }

        $creditmemo->setShippingAmount($shippingAmount);
        $creditmemo->setBaseShippingAmount($baseShippingAmount);

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal()+$shippingAmount);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()+$baseShippingAmount);
        return $this;
    }
}
