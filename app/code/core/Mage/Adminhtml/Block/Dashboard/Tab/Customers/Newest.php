<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml dashboard most recent customers grid
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Dashboard_Tab_Customers_Newest extends Mage_Adminhtml_Block_Dashboard_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customersNewestGrid');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('reports/customer_collection')
            ->addCustomerName();

        $storeFilter = 0;
        if ($this->getParam('store')) {
            $collection->addAttributeToFilter('store_id', $this->getParam('store'));
            $storeFilter = 1;
        } elseif ($this->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($this->getParam('website'))->getStoreIds();
            $collection->addAttributeToFilter('store_id', ['in' => $storeIds]);
        } elseif ($this->getParam('group')) {
            $storeIds = Mage::app()->getGroup($this->getParam('group'))->getStoreIds();
            $collection->addAttributeToFilter('store_id', ['in' => $storeIds]);
        }

        $collection->addOrdersStatistics($storeFilter)
            ->orderByCustomerRegistration();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', [
            'header'    => $this->__('Customer Name'),
            'sortable'  => false,
            'index'     => 'name',
        ]);

        $this->addColumn('orders_count', [
            'header'    => $this->__('Number of Orders'),
            'sortable'  => false,
            'index'     => 'orders_count',
            'type'      => 'number',
        ]);

        $baseCurrencyCode = (string) Mage::app()->getStore((int) $this->getParam('store'))->getBaseCurrencyCode();

        $this->addColumn('orders_avg_amount', [
            'header'    => $this->__('Average Order Amount'),
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'orders_avg_amount',
            'renderer'  => 'adminhtml/report_grid_column_renderer_currency',
        ]);

        $this->addColumn('orders_sum_amount', [
            'header'    => $this->__('Total Order Amount'),
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'orders_sum_amount',
            'renderer'  => 'adminhtml/report_grid_column_renderer_currency',
        ]);

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    /**
     * @param Mage_Customer_Model_Customer $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/customer/edit', ['id' => $row->getId()]);
    }
}
