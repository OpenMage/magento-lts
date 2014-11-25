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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin application last orders renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_LastOrders extends Mage_Adminhtml_Block_Dashboard_Orders_Grid
{
    /**
     * Last orders count limit
     */
    const LAST_ORDER_COUNT_LIMIT = 5;

    /**
     * Add last orders info to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_LastOrders
     */
    public function addLastOrdersToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $xmlObj)
    {
        if (!Mage::helper('core')->isModuleEnabled('Mage_Reports')) {
            return $this;
        }

        /** @var $collection Mage_Reports_Model_Resource_Order_Collection */
        $collection = Mage::getResourceModel('reports/order_collection')->addItemCountExpr()
            ->joinCustomerName('customer')->orderByCreatedAt()->setPageSize(self::LAST_ORDER_COUNT_LIMIT);

        foreach (Mage::helper('xmlconnect/adminApplication')->getSwitcherList() as $storeId) {
            if ($storeId) {
                $collection->addAttributeToFilter('store_id', $storeId);
                $collection->addRevenueToSelect();
            } else {
                $collection->addRevenueToSelect(true);
            }

            $this->setCollection($collection);
            $orderList = $this->_prepareColumns()->getCollection()->load();
            $valuesXmlObj = $xmlObj->addCustomChild('values', null, array(
                'store_id' => $storeId ? $storeId : Mage_XmlConnect_Helper_AdminApplication::ALL_STORE_VIEWS
            ));

            foreach ($orderList as $order) {
                $itemXmlObj = $valuesXmlObj->addCustomChild('item');
                $itemXmlObj->addCustomChild('customer', $order->getCustomer(), array('label' => $this->__('Customer')));
                $itemXmlObj->addCustomChild('items_count', $order->getItemsCount(), array(
                    'label' => $this->__('Items')
                ));
                $currency_code = Mage::app()->getStore($storeId)->getBaseCurrencyCode();
                $itemXmlObj->addCustomChild('currency', Mage::app()->getLocale()->currency($currency_code)
                    ->toCurrency($order->getRevenue()), array('label' => $this->__('Grand Total')));
            }
            $collection->clear();
        }
        return $this;
    }
}
