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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml tagged products grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Tag_Grid_Products extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('tag/product_collection')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
        ;
        if ($tagId = $this->getRequest()->getParam('tag_id')) {
            $collection->addTagFilter($tagId);
        }
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            $collection->addCustomerFilter($customerId);
        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('product_id', array(
            'header'    => Mage::helper('tag')->__('ID'),
            'align'     => 'center',
            'width'     => '60px',
            'sortable'  => false,
            'index'     => 'product_id'
        ));
        $this->addColumn('sku', array(
            'header'    => Mage::helper('tag')->__('SKU'),
            'align'     => 'center',
            'index'     => 'sku'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('tag')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('tags', array(
            'header'    => Mage::helper('tag')->__('Tags'),
            'index'     => 'tags',
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'adminhtml/tag_grid_column_renderer_tags'
        ));
        $this->addColumn('action', array(
            'header'    => Mage::helper('tag')->__('Action'),
            'align'     => 'center',
            'width'     => '120px',
            'format'    => '<a href="'.$this->getUrl('*/*/customers/product_id/$product_id').'">'.Mage::helper('tag')->__('View Customers').'</a>',
            'filter'    => false,
            'sortable'  => false,
            'is_system' => true
        ));

        return parent::_prepareColumns();
    }

}

