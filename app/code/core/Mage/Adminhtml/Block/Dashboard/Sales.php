<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml dashboard sales statistics bar
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Dashboard_Sales extends Mage_Adminhtml_Block_Dashboard_Bar
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('dashboard/salebar.phtml');
    }

    /**
     * @throws Mage_Core_Exception
     */
    protected function _prepareLayout()
    {
        if (!$this->isModuleEnabled('Mage_Reports')) {
            return $this;
        }

        $request = $this->getRequest();

        $isFilter = $request->getParam('store') || $request->getParam('website') || $request->getParam('group');

        $collection = Mage::getResourceModel('reports/order_collection')
            ->calculateSales($isFilter);

        if ($request->getParam('store')) {
            $collection->addFieldToFilter('store_id', $request->getParam('store'));
        } elseif ($request->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($request->getParam('website'))->getStoreIds();
            $collection->addFieldToFilter('store_id', ['in' => $storeIds]);
        } elseif ($request->getParam('group')) {
            $storeIds = Mage::app()->getGroup($request->getParam('group'))->getStoreIds();
            $collection->addFieldToFilter('store_id', ['in' => $storeIds]);
        }

        $collection->load();
        $sales = $collection->getFirstItem();

        $this->addTotal($this->__('Lifetime Sales'), $sales->getLifetime());
        $this->addTotal($this->__('Average Orders'), $sales->getAverage());
        return $this;
    }
}
