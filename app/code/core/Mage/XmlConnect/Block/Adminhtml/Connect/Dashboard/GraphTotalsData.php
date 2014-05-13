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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Totals chart data xml renderer block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphTotalsData extends Mage_Adminhtml_Block_Dashboard_Totals
{
    /**
     * Get rid of unnecessary collection initialization by parent
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphTotalsData
     */
    protected function _prepareLayout()
    {
        return $this;
    }

    /**
     * Init totals collection and assign totals values
     *
     * @param null|int $storeId
     * @param string $rangeId
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphTotalsData
     */
    protected function _initCollection($storeId, $rangeId)
    {
        if (!Mage::helper('core')->isModuleEnabled('Mage_Reports')) {
            return $this;
        }

        /* @var $collection Mage_Reports_Model_Mysql4_Order_Collection */
        $collection = Mage::getResourceModel('reports/order_collection')->addCreateAtPeriodFilter($rangeId)
            ->calculateTotals((bool)$storeId);

        if ($storeId) {
            $collection->addFieldToFilter('store_id', $storeId);
        } elseif (!$collection->isLive()) {
            $collection->addFieldToFilter('store_id', array(
                'eq' => Mage::app()->getStore(Mage_Core_Model_Store::ADMIN_CODE)->getId()
            ));
        }

        $collection->load();
        $totals = $collection->getFirstItem();

        $this->addTotal($this->__('Revenue'), $totals->getRevenue());
        $this->addTotal($this->__('Tax'), $totals->getTax());
        $this->addTotal($this->__('Shipping'), $totals->getShipping());
        $this->addTotal($this->__('Quantity'), $totals->getQuantity() * 1, true);
        return $this;
    }

    /**
     * Add cart totals data to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphTotalsData
     */
    public function addTotalsDataToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $xmlObj)
    {
        $dataValuesXml = $xmlObj->addCustomChild('chart_totals');

        foreach (Mage::helper('xmlconnect/adminApplication')->getSwitcherList() as $storeFilter) {
            $storeId = $storeFilter ? $storeFilter : null;

            $totalsXml = $dataValuesXml->addCustomChild('totals', null, array(
                'store_id' => $storeId ? $storeId : Mage_XmlConnect_Helper_AdminApplication::ALL_STORE_VIEWS
            ));

            foreach ($this->getRangeOptions() as $rangeFilter) {
                $this->_initCollection($storeId, $rangeFilter['value']);
                $valuesXml = $totalsXml->addCustomChild('values', null, array(
                    'range_id' => $rangeFilter['value']
                ));
                foreach ($this->getTotals() as $total) {
                    $totalValue = $valuesXml->escapeXml($total['value'] . $total['decimals']);
                    $valuesXml->addCustomChild('item', $totalValue, array('label' => $total['label']));
                }
                $this->_clearTotals();
            }
        }
        return $this;
    }

    /**
     * Clear totals values array
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphTotalsData
     */
    protected function _clearTotals()
    {
        $this->_totals = array();
        return $this;
    }
}
