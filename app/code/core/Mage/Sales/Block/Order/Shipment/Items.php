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
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

    public function getPrintShipmentUrl($shipment){
        return Mage::getUrl('*/*/printShipment', array('shipment_id' => $shipment->getId()));
    }

    public function getPrintAllShipmentsUrl($order){
        return Mage::getUrl('*/*/printShipment', array('order_id' => $order->getId()));
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
