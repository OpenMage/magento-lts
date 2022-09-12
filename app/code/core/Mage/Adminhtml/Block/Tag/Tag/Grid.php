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
 * Adminhtml all tags grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Tag_Model_Resource_Tag_Collection getCollection()
 */
class Mage_Adminhtml_Block_Tag_Tag_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_tag_grid')
             ->setDefaultSort('name')
             ->setDefaultDir('ASC')
             ->setUseAjax(true)
             ->setSaveParametersInSession(true);
    }

    /**
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getIndex() === 'stores') {
            $this->getCollection()->addStoreFilter($column->getFilter()->getCondition(), false);
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @inheritDoc
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('tag/tag_collection')
            ->addSummary(Mage::app()->getStore()->getId())
            ->addStoresVisibility();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', [
            'header'        => Mage::helper('tag')->__('Tag'),
            'index'         => 'name',
        ]);

        $this->addColumn('products', [
            'header'        => Mage::helper('tag')->__('Products'),
            'width'         => 140,
            'align'         => 'right',
            'index'         => 'products',
            'type'          => 'number',
        ]);

        $this->addColumn('customers', [
            'header'        => Mage::helper('tag')->__('Customers'),
            'width'         => 140,
            'align'         => 'right',
            'index'         => 'customers',
            'type'          => 'number',
        ]);

        /** @var Mage_Tag_Helper_Data $helper */
        $helper = $this->helper('tag/data');
        $this->addColumn('status', [
            'header'        => Mage::helper('tag')->__('Status'),
            'width'         => 90,
            'index'         => 'status',
            'type'          => 'options',
            'options'       => $helper->getStatusesArray(),
        ]);

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('visible_in', [
                'header'                => Mage::helper('tag')->__('Store View'),
                'type'                  => 'store',
                'skipAllStoresLabel'    => true,
                'index'                 => 'stores',
                'sortable'              => false,
                'store_view'            => true
            ]);
        }

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('tag_id');
        $this->getMassactionBlock()->setFormFieldName('tag');

        $this->getMassactionBlock()->addItem('delete', [
             'label'    => Mage::helper('tag')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('tag')->__('Are you sure?')
        ]);

        /** @var Mage_Tag_Helper_Data $helper */
        $helper = $this->helper('tag/data');
        $statuses = $helper->getStatusesOptionsArray();

        array_unshift($statuses, ['label'=>'', 'value'=>'']);

        $this->getMassactionBlock()->addItem('status', [
            'label'=> Mage::helper('tag')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', ['_current'=>true]),
            'additional' => [
                'visibility' => [
                    'name'     => 'status',
                    'type'     => 'select',
                    'class'    => 'required-entry',
                    'label'    => Mage::helper('tag')->__('Status'),
                    'values'   => $statuses
                ]
            ]
        ]);

        return $this;
    }

    /**
     * Retrieves Grid Url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/tag/ajaxGrid', ['_current' => true]);
    }

    /**
     * Retrieves row click URL
     *
     * @param  Mage_Tag_Model_Tag $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['tag_id' => $row->getId()]);
    }
}
