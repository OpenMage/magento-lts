<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Index
 */

use Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract as MassAction;

/**
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
     * @throws Exception
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
     * @throws Mage_Core_Exception
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
     * @throws Exception
     */
    protected function _prepareColumns()
    {
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
            'options'   => $this->_processModel->getModesOptions(),
        ]);

        $this->addColumn('status', [
            'header'    => Mage::helper('index')->__('Status'),
            'width'     => '120',
            'align'     => 'left',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => $this->_processModel->getStatusesOptions(),
            'frame_callback' => [$this, 'decorateStatus'],
        ]);

        $this->addColumn('update_required', [
            'header'    => Mage::helper('index')->__('Update Required'),
            'sortable'  => false,
            'width'     => '120',
            'align'     => 'left',
            'index'     => 'update_required',
            'type'      => 'options',
            'options'   => $this->_processModel->getUpdateRequiredOptions(),
            'frame_callback' => [$this, 'decorateUpdateRequired'],
        ]);

        $this->addColumn('ended_at', [
            'header'    => Mage::helper('index')->__('Updated At'),
            'type'      => 'datetime',
            'align'     => 'left',
            'index'     => 'ended_at',
            'frame_callback' => [$this, 'decorateDate'],
        ]);

        $this->addColumn(
            'action',
            [
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => [
                    [
                        'caption'   => Mage::helper('index')->__('Reindex Data'),
                        'url'       => ['base' => '*/*/reindexProcess'],
                        'field'     => 'process',
                    ],
                ],
                'is_system' => true,
            ],
        );

        parent::_prepareColumns();

        return $this;
    }

    /**
     * Decorate status column values
     *
     * @param string                                  $value
     * @param Mage_Index_Model_Process                $row
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @param bool                                    $isExport
     *
     * @return string
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        $class = '';
        switch ($row->getStatus()) {
            case Mage_Index_Model_Process::STATUS_PENDING:
                $class = self::CSS_SEVERITY_NOTICE;
                break;
            case Mage_Index_Model_Process::STATUS_RUNNING:
                $class = self::CSS_SEVERITY_MAJOR;
                break;
            case Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX:
                $class = self::CSS_SEVERITY_CRITICAL;
                break;
        }

        return sprintf(self::PATTERN_SEVERITY, $class, $value);
    }

    /**
     * Decorate "Update Required" column values
     *
     * @param string                                  $value
     * @param Mage_Index_Model_Process                $row
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @param bool                                    $isExport
     *
     * @return string
     */
    public function decorateUpdateRequired($value, $row, $column, $isExport)
    {
        $class = '';
        switch ($row->getUpdateRequired()) {
            case 0:
                $class = self::CSS_SEVERITY_NOTICE;
                break;
            case 1:
                $class = self::CSS_SEVERITY_CRITICAL;
                break;
        }

        return sprintf(self::PATTERN_SEVERITY, $class, $value);
    }

    /**
     * Decorate last run date coumn
     *
     * @param string                                  $value
     * @param Mage_Index_Model_Process                $row
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @param bool                                    $isExport
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
                    'values'    => $modeOptions,
                ],
            ],
        ]);

        $this->getMassactionBlock()->addItem(MassAction::REINDEX, [
            'label'    => Mage::helper('index')->__('Reindex Data'),
            'url'      => $this->getUrl('*/*/massReindex'),
            'selected' => true,
        ]);

        return $this;
    }
}
