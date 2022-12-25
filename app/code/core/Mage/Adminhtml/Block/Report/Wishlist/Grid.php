<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml wishlist report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Wishlist_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('wishlistReportGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('reports/wishlist_product_collection')
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('name')
            ->addWishlistCount();

        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', [
            'header'    => Mage::helper('reports')->__('ID'),
            'width'     => '50px',
            'index'     => 'entity_id'
        ]);

        $this->addColumn('name', [
            'header'    => Mage::helper('reports')->__('Name'),
            'index'     => 'name'
        ]);

        $this->addColumn('wishlists', [
            'header'    => Mage::helper('reports')->__('Wishlists'),
            'width'     => '50px',
            'align'     => 'right',
            'index'     => 'wishlists'
        ]);

        $this->addColumn('bought_from_wishlists', [
            'header'    => Mage::helper('reports')->__('Bought from wishlists'),
            'width'     => '50px',
            'align'     => 'right',
            'sortable'  => false,
            'index'     => 'bought_from_wishlists'
        ]);

        $this->addColumn('w_vs_order', [
            'header'    => Mage::helper('reports')->__('Wishlist vs. Regular Order'),
            'width'     => '50px',
            'align'     => 'right',
            'sortable'  => false,
            'index'     => 'w_vs_order'
        ]);

        $this->addColumn('num_deleted', [
            'header'    => Mage::helper('reports')->__('Number of Times Deleted'),
            'width'     => '50px',
            'align'     => 'right',
            'sortable'  => false,
            'index'     => 'num_deleted'
        ]);

        $this->addExportType('*/*/exportWishlistCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportWishlistExcel', Mage::helper('reports')->__('Excel XML'));

        $this->setFilterVisibility(false);

        return parent::_prepareColumns();
    }
}
