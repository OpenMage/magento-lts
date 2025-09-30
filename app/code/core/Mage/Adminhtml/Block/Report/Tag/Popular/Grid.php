<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml popular tags report grid block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Tag_Popular_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('grid');
    }

    protected function _prepareCollection()
    {
        if ($this->getRequest()->getParam('website')) {
            $storeId = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
        } elseif ($this->getRequest()->getParam('group')) {
            $storeId = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
        } elseif ($this->getRequest()->getParam('store')) {
            $storeId = (int) $this->getRequest()->getParam('store');
        } else {
            $storeId = '';
        }

        $collection = Mage::getResourceModel('reports/tag_collection')
            ->addPopularity($storeId)
            ->addStatusFilter(Mage_Tag_Model_Tag::STATUS_APPROVED);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', [
            'header'    => Mage::helper('reports')->__('Tag Name'),
            'index'     => 'name',
        ]);

        $this->addColumn('taged', [
            'header'    => Mage::helper('reports')->__('Popularity'),
            'width'     => '50px',
            'align'     => 'right',
            'index'     => 'popularity',
        ]);

        $this->addColumn(
            'action',
            [
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => [
                    [
                        'caption' => Mage::helper('catalog')->__('Show Details'),
                        'url'     => [
                            'base' => '*/*/tagDetail',
                        ],
                        'field'   => 'id',
                    ],
                ],
                'is_system' => true,
                'index'     => 'stores',
            ],
        );
        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportPopularCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportPopularExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/tagDetail', ['id' => $row->getTagId()]);
    }
}
