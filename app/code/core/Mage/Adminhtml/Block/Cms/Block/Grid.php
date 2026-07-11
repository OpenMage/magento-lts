<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

use Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract as MassAction;

/**
 * Adminhtml cms blocks grid
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Cms_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected string $_eventPrefix = 'adminhtml_cms_block_grid';

    public function __construct()
    {
        parent::__construct();
        $this->setId('cmsBlockGrid');
        $this->setDefaultSort('title');
        $this->setDefaultDir('ASC');
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    #[Override]
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('cms/block')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    #[Override]
    protected function _prepareColumns()
    {
        $this->addColumn('title', [
            'header'    => Mage::helper('cms')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ]);

        $this->addColumn('identifier', [
            'header'    => Mage::helper('cms')->__('Identifier'),
            'align'     => 'left',
            'index'     => 'identifier',
        ]);

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', [
                'header'        => Mage::helper('cms')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback' => $this->_filterStoreCondition(...),
            ]);
        }

        $this->addColumn('is_active', [
            'header'    => Mage::helper('cms')->__('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => [
                0 => Mage::helper('cms')->__('Disabled'),
                1 => Mage::helper('cms')->__('Enabled'),
            ],
        ]);

        $this->addColumn('creation_time', [
            'header'    => Mage::helper('cms')->__('Date Created'),
            'index'     => 'creation_time',
            'type'      => 'datetime',
        ]);

        $this->addColumn('update_time', [
            'header'    => Mage::helper('cms')->__('Last Modified'),
            'index'     => 'update_time',
            'type'      => 'datetime',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        return parent::_afterLoadCollection();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('block_id');
        $this->getMassactionBlock()->setFormFieldName('block');

        if ($this->_isAllowedAction('delete')) {
            $this->getMassactionBlock()->addItem(MassAction::DELETE, [
                'label' => Mage::helper('cms')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
            ]);
        }

        if ($this->_isAllowedAction('save')) {
            $statuses = [
                1 => Mage::helper('cms')->__('Enabled'),
                0 => Mage::helper('cms')->__('Disabled'),
            ];

            array_unshift($statuses, '');
            $this->getMassactionBlock()->addItem(MassAction::STATUS, [
                'label' => Mage::helper('cms')->__('Change status'),
                'url' => $this->getUrl('*/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('cms')->__('Status'),
                        'values' => $statuses,
                    ],
                ],
            ]);
        }

        return parent::_prepareMassaction();
    }

    /**
     * @param Mage_Cms_Model_Resource_Block_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column  $column
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if ($value = $column->getFilter()->getValue()) {
            $collection->addStoreFilter($value);
        }
    }

    /**
     * @inheritDoc
     * @param  Mage_Cms_Model_Block $row
     * @throws Mage_Core_Exception
     */
    #[Override]
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['block_id' => $row->getId()]);
    }

    /**
     * Check permission for passed action
     */
    protected function _isAllowedAction(string $action): bool
    {
        return $this->isAllowed('cms/block/' . $action);
    }
}
