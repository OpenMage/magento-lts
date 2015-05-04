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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml customer orders grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Cart extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setUseAjax(true);
        $this->_parentTemplate = $this->getTemplate();
        $this->setTemplate('customer/tab/cart.phtml');
    }

    /**
     * Prepare grid
     *
     * @return void
     */
    protected function _prepareGrid()
    {
        $this->setId('customer_cart_grid' . $this->getWebsiteId());
        parent::_prepareGrid();
    }

    protected function _prepareCollection()
    {
        $customer = Mage::registry('current_customer');
        $storeIds = Mage::app()->getWebsite($this->getWebsiteId())->getStoreIds();

        $quote = Mage::getModel('sales/quote')
            ->setSharedStoreIds($storeIds)
            ->loadByCustomer($customer);

        if ($quote) {
            $collection = $quote->getItemsCollection(false);
        }
        else {
            $collection = new Varien_Data_Collection();
        }

        $collection->addFieldToFilter('parent_item_id', array('null' => true));

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('product_id', array(
            'header'    => Mage::helper('catalog')->__('Product ID'),
            'index'     => 'product_id',
            'width'     => '100px',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Product Name'),
            'index'     => 'name',
            'renderer'  => 'adminhtml/customer_edit_tab_view_grid_renderer_item'
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'index'     => 'sku',
            'width'     => '100px',
        ));

        $this->addColumn('qty', array(
            'header'    => Mage::helper('catalog')->__('Qty'),
            'index'     => 'qty',
            'type'      => 'number',
            'width'     => '60px',
        ));

        $this->addColumn('price', array(
            'header'        => Mage::helper('catalog')->__('Price'),
            'index'         => 'price',
            'type'          => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));

        $this->addColumn('total', array(
            'header'        => Mage::helper('sales')->__('Total'),
            'index'         => 'row_total',
            'type'          => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('customer')->__('Action'),
            'index'     => 'quote_item_id',
            'renderer'  => 'adminhtml/customer_grid_renderer_multiaction',
            'filter'    => false,
            'sortable'  => false,
            'actions'   => array(
                array(
                    'caption'           => Mage::helper('customer')->__('Configure'),
                    'url'               => 'javascript:void(0)',
                    'process'           => 'configurable',
                    'control_object'    => $this->getJsObjectName() . 'cartControl'
                ),
                array(
                    'caption'   => Mage::helper('customer')->__('Delete'),
                    'url'       => '#',
                    'onclick'   => 'return ' . $this->getJsObjectName() . 'cartControl.removeItem($item_id);'
                )
            )
        ));

        return parent::_prepareColumns();
    }

    /**
     * Gets customer assigned to this block
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer() {
        return Mage::registry('current_customer');
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/cart', array('_current'=>true, 'website_id' => $this->getWebsiteId()));
    }

    public function getGridParentHtml()
    {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative'=>true));
        return $this->fetchView($templateName);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product/edit', array('id' => $row->getProductId()));
    }
}
