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
 * Adminhtml tags detail for product report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Tag_Product_Detail_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('grid');
    }

    protected function _prepareCollection()
    {

        $collection = Mage::getResourceModel('reports/tag_product_collection');

        $collection->addTagedCount()
            ->addProductFilter($this->getRequest()->getParam('id'))
            ->addStatusFilter(Mage::getModel('tag/tag')->getApprovedStatus())
            ->addStoresVisibility()
            ->setActiveFilter()
            ->addGroupByTag()
            ->setRelationId();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('tag_name', array(
            'header'    =>Mage::helper('reports')->__('Tag Name'),
            'index'     =>'tag_name'
        ));

        $this->addColumn('taged', array(
            'header'    =>Mage::helper('reports')->__('Tag Use'),
            'index'     =>'taged',
            'align'     => 'right'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('visible', array(
                'header'    => Mage::helper('reports')->__('Visible In'),
                'sortable'  => false,
                'index'     => 'stores',
                'type'      => 'store',
                'store_view'=> true
            ));
        }

        $this->addExportType('*/*/exportProductDetailCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportProductDetailExcel', Mage::helper('reports')->__('Excel XML'));

        $this->setFilterVisibility(false);

        return parent::_prepareColumns();
    }

}

