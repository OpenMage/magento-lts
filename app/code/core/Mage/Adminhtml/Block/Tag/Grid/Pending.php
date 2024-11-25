<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

use Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract as MassAction;

/**
 * Adminhtml pending tags grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 *
 * @method Mage_Tag_Model_Resource_Tag_Collection getCollection()
 */
class Mage_Adminhtml_Block_Tag_Grid_Pending extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('pending_grid')
             ->setDefaultSort('name')
             ->setDefaultDir('ASC')
             ->setUseAjax(true)
             ->setSaveParametersInSession(true);
    }

    /**
     * @inheritDoc
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('tag/tag_collection')
            ->addSummary(0)
            ->addStoresVisibility()
            ->addStatusFilter(Mage_Tag_Model_Tag::STATUS_PENDING);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('name', [
            'header'        => Mage::helper('tag')->__('Tag'),
            'index'         => 'name'
        ]);

        $this->addColumn('products', [
            'header'        => Mage::helper('tag')->__('Products'),
            'width'         => '140px',
            'index'         => 'products',
            'type'          => 'number'
        ]);

        $this->addColumn('customers', [
            'header'        => Mage::helper('tag')->__('Customers'),
            'width'         => '140px',
            'index'         => 'customers',
            'type'          => 'number'
        ]);

        // Collection for stores filters
        if (!$collection = Mage::registry('stores_select_collection')) {
            $collection =  Mage::app()->getStore()->getResourceCollection()
                ->load();
            Mage::register('stores_select_collection', $collection);
        }

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('visible_in', [
                'type'      => 'store',
                'index'     => 'stores',
                'sortable'  => false,
            ]);
        }

        return parent::_prepareColumns();
    }

    /**
     * Retrieves row click URL
     *
     * @param Varien_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['tag_id' => $row->getId(), 'ret' => 'pending']);
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
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('tag_id');
        $this->getMassactionBlock()->setFormFieldName('tag');

        $this->getMassactionBlock()->addItem(MassAction::DELETE, [
             'label' => Mage::helper('tag')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete', ['ret' => 'pending'])
        ]);

        /** @var Mage_Tag_Helper_Data $helper */
        $helper = $this->helper('tag/data');
        $statuses = $helper->getStatusesOptionsArray();

        array_unshift($statuses, ['label' => '', 'value' => '']);

        $this->getMassactionBlock()->addItem(MassAction::STATUS, [
             'label' => Mage::helper('tag')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', ['_current' => true, 'ret' => 'pending']),
             'additional' => [
                    'visibility' => [
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('tag')->__('Status'),
                         'values' => $statuses
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
        return $this->getUrl('*/tag/ajaxPendingGrid', ['_current' => true]);
    }
}
