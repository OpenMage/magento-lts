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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml customer view wishlist block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_View_Wishlist extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_view_wishlist_grid');
        $this->setSortable(false);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setEmptyText(Mage::helper('customer')->__("There are no items in customer's wishlist at the moment"));
    }

    protected function _prepareCollection()
    {
        $wishlist = Mage::getModel('wishlist/wishlist');
        $collection = $wishlist->loadByCustomer(Mage::registry('current_customer'))
            ->setSharedStoreIds($wishlist->getSharedStoreIds(false))
            ->getProductCollection()
            ->addAttributeToSelect('name')
            ->addStoreData();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('product_id', array(
            'header'    => Mage::helper('customer')->__('Product ID'),
            'index'     => 'product_id',
            'type'      => 'number',
            'width'     => '100px'
        ));

        $this->addColumn('product_name', array(
            'header'    => Mage::helper('customer')->__('Product Name'),
            'index'     => 'name'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store', array(
                'header'    => Mage::helper('customer')->__('Added From'),
                'index'     => 'store_id',
                'type'      => 'store',
                'width'     => '160px',
            ));
        }

        $this->addColumn('added_at', array(
            'header'    => Mage::helper('customer')->__('Date Added'),
            'index'     => 'added_at',
            'type'      => 'date',
            'width'     => '140px',
        ));

        $this->addColumn('days', array(
            'header'    => Mage::helper('customer')->__('Days in Wishlist'),
            'index'     => 'days_in_wishlist',
            'type'      => 'number',
            'width'     => '140px',
        ));

        return parent::_prepareColumns();
    }

    public function getHeadersVisibility()
    {
        return ($this->getCollection()->getSize() > 0);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product/edit', array('id' => $row->getProductId()));
    }

}

