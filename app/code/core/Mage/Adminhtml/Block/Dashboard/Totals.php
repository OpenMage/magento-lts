<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml dashboard totals bar
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Dashboard_Totals extends Mage_Adminhtml_Block_Dashboard_Bar
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('dashboard/totalbar.phtml');
    }

    /**
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     * @throws Exception
     */
    protected function _prepareLayout()
    {
        if (!$this->isModuleEnabled('Mage_Reports')) {
            return $this;
        }

        $request = $this->getRequest();

        $isFilter = $request->getParam('store') || $request->getParam('website') || $request->getParam('group');
        $period = $request->getParam('period', '24h');

        /** @var Mage_Reports_Model_Resource_Order_Collection $collection */
        $collection = Mage::getResourceModel('reports/order_collection')
            ->addCreateAtPeriodFilter($period)
            ->calculateTotals($isFilter);

        if ($request->getParam('store')) {
            $collection->addFieldToFilter('store_id', $request->getParam('store'));
        } elseif ($request->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($request->getParam('website'))->getStoreIds();
            $collection->addFieldToFilter('store_id', ['in' => $storeIds]);
        } elseif ($request->getParam('group')) {
            $storeIds = Mage::app()->getGroup($request->getParam('group'))->getStoreIds();
            $collection->addFieldToFilter('store_id', ['in' => $storeIds]);
        } elseif (!$collection->isLive()) {
            $collection->addFieldToFilter(
                'store_id',
                ['eq' => Mage::app()->getStore(Mage_Core_Model_Store::ADMIN_CODE)->getId()],
            );
        }

        $collection->load();

        $totals = $collection->getFirstItem();

        $this->addTotal($this->__('Revenue'), $totals->getRevenue());
        $this->addTotal($this->__('Tax'), $totals->getTax());
        $this->addTotal($this->__('Shipping'), $totals->getShipping());
        $this->addTotal($this->__('Quantity'), $totals->getQuantity() * 1, true);
        return $this;
    }
}
