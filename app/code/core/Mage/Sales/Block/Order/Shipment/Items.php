<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales order view items block
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Order_Shipment_Items extends Mage_Sales_Block_Items_Abstract
{
    /**
     * Retrieve current order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @return string
     */
    public function getPrintShipmentUrl($shipment)
    {
        return Mage::getUrl('*/*/printShipment', ['shipment_id' => $shipment->getId()]);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getPrintAllShipmentsUrl($order)
    {
        return Mage::getUrl('*/*/printShipment', ['order_id' => $order->getId()]);
    }

    /**
     * Get html of shipment comments block
     *
     * @param   Mage_Sales_Model_Order_Shipment $shipment
     * @return  string
     */
    public function getCommentsHtml($shipment)
    {
        $html = '';
        $comments = $this->getChild('shipment_comments');
        if ($comments) {
            $comments->setEntity($shipment)
                ->setTitle(Mage::helper('sales')->__('About Your Shipment'));
            $html = $comments->toHtml();
        }
        return $html;
    }
}
