<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml dashboard totals bar
 *
 * @category   Mage
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
