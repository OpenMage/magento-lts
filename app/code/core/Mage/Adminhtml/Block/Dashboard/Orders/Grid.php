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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml dashboard recent orders grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Dashboard_Orders_Grid extends Mage_Adminhtml_Block_Dashboard_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('lastOrdersGrid');
    }

    protected function _prepareCollection()
    {
        //TODO: add full name logic
        $collection = Mage::getResourceModel('reports/order_collection')
            ->addItemCountExpr()
            ->addExpressionAttributeToSelect('customer',
                "IFNULL(CONCAT({{customer_firstname}},' ',{{customer_lastname}}), '{$this->__('Guest')}')",
                array('customer_firstname','customer_lastname')
            )
            ->setOrder('created_at');

        if($this->getParam('store') || $this->getParam('website') || $this->getParam('group')) {
            if ($this->getParam('store')) {
                $collection->addAttributeToFilter('store_id', $this->getParam('store'));
            } else if ($this->getParam('website')){
                $storeIds = Mage::app()->getWebsite($this->getParam('website'))->getStoreIds();
                $collection->addAttributeToFilter('store_id', array('in' => $storeIds));
            } else if ($this->getParam('group')){
                $storeIds = Mage::app()->getGroup($this->getParam('group'))->getStoreIds();
                $collection->addAttributeToFilter('store_id', array('in' => $storeIds));
            }

            $collection->addExpressionAttributeToSelect('revenue',
                '({{base_grand_total}})',
                array('base_grand_total'));
        } else {
            $collection->addExpressionAttributeToSelect('revenue',
                '({{base_grand_total}}*{{base_to_global_rate}})',
                array('base_grand_total', 'base_to_global_rate'));
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('customer', array(
            'header'    => $this->__('Customer'),
            'sortable'  => false,
            'index'     => 'customer'
        ));

        $this->addColumn('items', array(
            'header'    => $this->__('Items'),
            'align'     => 'right',
            'sortable'  => false,
            'index'     => 'items_count'
        ));

        $baseCurrencyCode = Mage::app()->getStore((int)$this->getParam('store'))->getBaseCurrencyCode();

        $this->addColumn('total', array(
            'header'    => $this->__('Grand Total'),
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'currency',
            'currency_code'  => $baseCurrencyCode,
            'index'     => 'revenue'
        ));

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/sales_order/view', array('order_id'=>$row->getId()));
    }
}
