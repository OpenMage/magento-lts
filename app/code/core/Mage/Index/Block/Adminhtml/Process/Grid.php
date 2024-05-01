<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Index
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

use Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract as MassAction;

/**
 * @category   Mage
 * @package    Mage_Index
 */
class Mage_Index_Block_Adminhtml_Process_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Process model
     *
     * @var Mage_Index_Model_Process
     */
    protected $_processModel;

    /**
     * Mass-action block
     *
     * @var string
     */
    protected $_massactionBlockName = 'index/adminhtml_process_grid_massaction';

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_processModel = Mage::getSingleton('index/process');
        $this->setId('indexer_processes_grid');
        $this->_filterVisibility = false;
        $this->_pagerVisibility  = false;
    }

    /**
     * Prepare grid collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('index/process_collection');
        $this->setCollection($collection);
        parent::_prepareCollection();

        return $this;
    }

    /**
     * Add name and description to collection elements
     *
     * @return $this
     */
    protected function _afterLoadCollection()
    {
        /** @var Mage_Index_Model_Process $item */
        foreach ($this->_collection as $key => $item) {
            if (!$item->getIndexer()->isVisible()) {
                $this->_collection->removeItemByKey($key);
                continue;
            }
            $item->setName($item->getIndexer()->getName());
            $item->setDescription($item->getIndexer()->getDescription());
            $item->setUpdateRequired($item->getUnprocessedEventsCollection()->count() > 0 ? 1 : 0);
            if ($item->isLocked()) {
                $item->setStatus(Mage_Index_Model_Process::STATUS_RUNNING);
            }
        }
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();
        $this->addColumn('indexer_code', [
            'header'    => Mage::helper('index')->__('Index'),
            'width'     => '180',
            'align'     => 'left',
            'index'     => 'name',
            'sortable'  => false,
        ]);

        $this->addColumn('description', [
            'header'    => Mage::helper('index')->__('Description'),
            'align'     => 'left',
            'index'     => 'description',
            'sortable'  => false,
        ]);

        $this->addColumn('mode', [
            'header'    => Mage::helper('index')->__('Mode'),
            'width'     => '150',
            'align'     => 'left',
            'index'     => 'mode',
            'type'      => 'options',
            'options'   => $this->_processModel->getModesOptions()
        ]);

        $this->addColumn('status', [
            'header'    => Mage::helper('index')->__('Status'),
            'width'     => '120',
            'align'     => 'left',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => $this->_processModel->getStatusesOptions(),
            'frame_callback' => [$this, 'decorateStatus']
        ]);

        $this->addColumn('update_required', [
            'header'    => Mage::helper('index')->__('Update Required'),
            'sortable'  => false,
            'width'     => '120',
            'align'     => 'left',
            'index'     => 'update_required',
            'type'      => 'options',
            'options'   => $this->_processModel->getUpdateRequiredOptions(),
            'frame_callback' => [$this, 'decorateUpdateRequired']
        ]);

        $this->addColumn('ended_at', [
            'header'    => Mage::helper('index')->__('Updated At'),
            'type'      => 'datetime',
            'align'     => 'left',
            'index'     => 'ended_at',
            'frame_callback' => [$this, 'decorateDate']
        ]);

        $this->addColumn(
            'action',
            [
                'header'    =>  Mage::helper('index')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => [
                    [
                        'caption'   => Mage::helper('index')->__('Reindex Data'),
                        'url'       => ['base' => '*/*/reindexProcess'],
                        'field'     => 'process'
                    ],
                ],
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
            ]
        );

        parent::_prepareColumns();

        return $this;
    }

    /**
     * Decorate status column values
     *
     * @param string $value
     * @param Mage_Index_Model_Process $row
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @param bool $isExport
     *
     * @return string
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        $class = '';
        switch ($row->getStatus()) {
            case Mage_Index_Model_Process::STATUS_PENDING:
                $class = 'grid-severity-notice';
                break;
            case Mage_Index_Model_Process::STATUS_RUNNING:
                $class = 'grid-severity-major';
                break;
            case Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX:
                $class = 'grid-severity-critical';
                break;
        }
        return '<span class="' . $class . '"><span>' . $value . '</span></span>';
    }

    /**
     * Decorate "Update Required" column values
     *
     * @param string $value
     * @param Mage_Index_Model_Process $row
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @param bool $isExport
     *
     * @return string
     */
    public function decorateUpdateRequired($value, $row, $column, $isExport)
    {
        $class = '';
        switch ($row->getUpdateRequired()) {
            case 0:
                $class = 'grid-severity-notice';
                break;
            case 1:
                $class = 'grid-severity-critical';
                break;
        }
        return '<span class="' . $class . '"><span>' . $value . '</span></span>';
    }

    /**
     * Decorate last run date coumn
     *
     * @param string $value
     * @param Mage_Index_Model_Process $row
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @param bool $isExport
     *
     * @return string
     */
    public function decorateDate($value, $row, $column, $isExport)
    {
        if (!$value) {
            return $this->__('Never');
        }
        return $value;
    }

    /**
     * Get row edit url
     *
     * @param Mage_Index_Model_Process $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['process' => $row->getId()]);
    }

    /**
     * Add mass-actions to grid
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('process_id');
        $this->getMassactionBlock()->setFormFieldName('process');

        $modeOptions = Mage::getModel('index/process')->getModesOptions();

        $this->getMassactionBlock()->addItem(MassAction::CHANGE_MODE, [
            'label'         => Mage::helper('index')->__('Change Index Mode'),
            'url'           => $this->getUrl('*/*/massChangeMode'),
            'additional'    => [
                'mode'      => [
                    'name'      => 'index_mode',
                    'type'      => 'select',
                    'class'     => 'required-entry',
                    'label'     => Mage::helper('index')->__('Index mode'),
                    'values'    => $modeOptions
                ]
            ]
        ]);

        $this->getMassactionBlock()->addItem(MassAction::REINDEX, [
            'label'    => Mage::helper('index')->__('Reindex Data'),
            'url'      => $this->getUrl('*/*/massReindex'),
            'selected' => true,
        ]);

        return $this;
    }
}
