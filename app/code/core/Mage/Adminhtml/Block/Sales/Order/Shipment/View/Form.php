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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shipment view form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Shipment_View_Form extends Mage_Adminhtml_Block_Sales_Order_Abstract
{
    /**
     * Retrieve shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        return Mage::registry('current_shipment');
    }

    /**
     * Retrieve invoice order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getShipment()->getOrder();
    }

    /**
     * Retrieve source
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getSource()
    {
        return $this->getShipment();
    }

    /**
     * Get create label button html
     *
     * @return string
     */
    public function getCreateLabelButton()
    {
        $data['shipment_id'] = $this->getShipment()->getId();
        $url = $this->getUrl('*/sales_order_shipment/createLabel', $data);
        return $this->getLayout()
            ->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'   => Mage::helper('sales')->__('Create Shipping Label...'),
                'onclick' => 'packaging.showWindow();',
            ))
            ->toHtml();
    }

    /**
     * Get print label button html
     *
     * @return string
     */
    public function getPrintLabelButton()
    {
        $data['shipment_id'] = $this->getShipment()->getId();
        $url = $this->getUrl('*/sales_order_shipment/printLabel', $data);
        return $this->getLayout()
            ->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'   => Mage::helper('sales')->__('Print Shipping Label'),
                'onclick' => 'setLocation(\'' . $url . '\')'
            ))
            ->toHtml();
    }

    /**
     * Show packages button html
     *
     * @return string
     */
    public function getShowPackagesButton()
    {
        return $this->getLayout()
            ->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'   => Mage::helper('sales')->__('Show Packages'),
                'onclick' => 'showPackedWindow();'
            ))
            ->toHtml();
    }

    /**
     * Check is carrier has functionality of creation shipping labels
     *
     * @return bool
     */
    public function canCreateShippingLabel()
    {
        $shippingCarrier = $this->getOrder()->getShippingCarrier();
        return $shippingCarrier && $shippingCarrier->isShippingLabelsAvailable();
    }
}
