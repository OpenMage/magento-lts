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
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Cache_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_invalidatedTypes = [];
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('cache_grid');
        $this->_filterVisibility = false;
        $this->_pagerVisibility  = false;
        $this->_invalidatedTypes = Mage::app()->getCacheInstance()->getInvalidatedTypes();
    }

    /**
     * Prepare grid collection
     */
    protected function _prepareCollection()
    {
        $collection = new Varien_Data_Collection();
        foreach (Mage::app()->getCacheInstance()->getTypes() as $type) {
            $collection->addItem($type);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add name and description to collection elements
     */
    protected function _afterLoadCollection()
    {
        return $this;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('cache_type', [
            'header'    => $this->__('Cache Type'),
            'width'     => '180',
            'align'     => 'left',
            'index'     => 'cache_type',
            'sortable'  => false,
        ]);

        $this->addColumn('description', [
            'header'    => $this->__('Description'),
            'align'     => 'left',
            'index'     => 'description',
            'sortable'  => false,
        ]);

        $this->addColumn('tags', [
            'header'    => $this->__('Associated Tags'),
            'align'     => 'left',
            'index'     => 'tags',
            'width'     => '180',
            'sortable'  => false,
        ]);

        $this->addColumn('status', [
            'header'    => $this->__('Status'),
            'width'     => '120',
            'align'     => 'left',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => [0 => $this->__('Disabled'), 1 => $this->__('Enabled')],
            'frame_callback' => [$this, 'decorateStatus'],
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Decorate status column values
     *
     * @return string
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        $class = '';
        if (isset($this->_invalidatedTypes[$row->getId()])) {
            $cell = '<span class="grid-severity-minor"><span>' . $this->__('Invalidated') . '</span></span>';
        } else {
            if ($row->getStatus()) {
                $cell = '<span class="grid-severity-notice"><span>' . $value . '</span></span>';
            } else {
                $cell = '<span class="grid-severity-critical"><span>' . $value . '</span></span>';
            }
        }
        return $cell;
    }

    /**
     * Get row edit url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }

    /**
     * Add mass-actions to grid
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('types');

        $modeOptions = Mage::getModel('index/process')->getModesOptions();

        $this->getMassactionBlock()->addItem(MassAction::ENABLE, [
            'label'    => Mage::helper('index')->__('Enable'),
            'url'      => $this->getUrl('*/*/massEnable'),
        ]);
        $this->getMassactionBlock()->addItem(MassAction::DISABLE, [
            'label'    => Mage::helper('index')->__('Disable'),
            'url'      => $this->getUrl('*/*/massDisable'),
        ]);
        $this->getMassactionBlock()->addItem(MassAction::REFRESH, [
            'label'    => Mage::helper('index')->__('Refresh'),
            'url'      => $this->getUrl('*/*/massRefresh'),
            'selected' => true,
        ]);

        return $this;
    }
}
