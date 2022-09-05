<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml tags by products report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Tag_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridProducts');
    }

    protected function _prepareCollection()
    {

        $collection = Mage::getResourceModel('reports/tag_product_collection');

        $collection->addUniqueTagedCount()
            ->addAllTagedCount()
            ->addStatusFilter(Mage::getModel('tag/tag')->getApprovedStatus())
            ->addGroupByProduct();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('entity_id', [
            'header'    =>Mage::helper('reports')->__('ID'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'entity_id'
        ]);

        $this->addColumn('name', [
            'header'    =>Mage::helper('reports')->__('Product Name'),
            'index'     =>'name'
        ]);

        $this->addColumn('utaged', [
            'header'    =>Mage::helper('reports')->__('Number of Unique Tags'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'utaged'
        ]);

        $this->addColumn('taged', [
            'header'    =>Mage::helper('reports')->__('Number of Total Tags'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'taged'
        ]);

        $this->addColumn('action',
            [
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '100%',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => [
                    [
                        'caption' => Mage::helper('catalog')->__('Show Tags'),
                        'url'     => [
                            'base'=>'*/*/productDetail'
                        ],
                        'field'   => 'id'
                    ]
                ],
                'is_system' => true,
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
            ]);

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportProductCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportProductExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/productDetail', ['id'=>$row->getId()]);
    }
}
