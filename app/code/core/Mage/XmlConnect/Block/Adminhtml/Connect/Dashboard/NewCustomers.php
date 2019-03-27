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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * New customers xml block renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_NewCustomers
    extends Mage_Adminhtml_Block_Dashboard_Tab_Customers_Newest
{
    /**
     * Customers count to display
     */
    const CUSTOMERS_COUNT_LIMIT = 5;

    /**
     * Get rid of unnecessary collection initialization
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_NewCustomers
     */
    protected function _prepareCollection()
    {
        return $this;
    }

    /**
     * Init new customers collection
     *
     * @param int|null $storeId
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_NewCustomers
     */
    protected function _initCollection($storeId)
    {
        /** @var $collection Mage_Reports_Model_Resource_Customer_Collection */
        $collection = Mage::getResourceModel('reports/customer_collection')->addCustomerName()
            ->setPageSize(self::CUSTOMERS_COUNT_LIMIT);

        $storeFilter = 0;
        if ($storeId) {
            $collection->addAttributeToFilter('store_id', $storeId);
            $storeFilter = 1;
        }

        $collection->addOrdersStatistics($storeFilter)->orderByCustomerRegistration();
        $this->setCollection($collection);
        return $this;
    }

    /**
     * Clear collection
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_NewCustomers
     */
    protected function _clearCollection()
    {
        $this->_collection = null;
        return $this;
    }

    /**
     * Add new customers statistic to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_NewCustomers
     */
    public function addNewCustomersToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $xmlObj)
    {
        foreach (Mage::helper('xmlconnect/adminApplication')->getSwitcherList() as $storeId) {
            $this->_clearCollection()->_initCollection($storeId);
            $valuesXml = $xmlObj->addCustomChild('values', null, array(
                'store_id' => $storeId ? $storeId : Mage_XmlConnect_Helper_AdminApplication::ALL_STORE_VIEWS
            ));

            if(!count($this->getCollection()->getItems()) > 0) {
                continue;
            }

            /** @var $orderHelper Mage_XmlConnect_Helper_Adminhtml_Dashboard_Order */
            $orderHelper = Mage::helper('xmlconnect/adminhtml_dashboard_order');

            foreach ($this->getCollection()->getItems() as $item) {
                $itemListXml = $valuesXml->addCustomChild('item');
                $itemListXml->addCustomChild('name', $item->getName(), array(
                    'label' => $this->__('Customer Name')
                ));
                $itemListXml->addCustomChild('orders_count', $item->getOrdersCount(), array(
                    'label' => $this->__('Number of Orders')
                ));
                $itemListXml->addCustomChild(
                    'orders_avg_amount',
                    $orderHelper->preparePrice($item->getOrdersAvgAmount(), $storeId),
                    array('label' => $this->__('Average Order Amount'))
                );
                $itemListXml->addCustomChild(
                    'orders_sum_amount',
                    $orderHelper->preparePrice($item->getOrdersSumAmount(), $storeId),
                    array('label' => $this->__('Total Order Amount'))
                );
            }
        }
        return $this;
    }
}
