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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Index
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Index_Block_Adminhtml_Process_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_processModel;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_processModel = Mage::getModel('index/process');
        $this->setId('indexer_processes_grid');
        $this->_filterVisibility = false;
        $this->_pagerVisibility  = false;
    }

    /**
     * Prepare grid collection
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('index/process_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add name and description to collection elements
     */
    protected function _afterLoadCollection()
    {
        foreach ($this->_collection as $item) {
            $item->setName($item->getIndexer()->getName());
            $item->setDescription($item->getIndexer()->getDescription());
        }
        return $this;
    }

    /**
     * Prepare grid columns
     */
    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();
        $this->addColumn('indexer_code', array(
            'header'    => Mage::helper('index')->__('Index'),
            'width'     => '180',
            'align'     => 'left',
            'index'     => 'name',
            'sortable'  => false,
        ));

        $this->addColumn('description', array(
            'header'    => Mage::helper('index')->__('Description'),
            'align'     => 'left',
            'index'     => 'description',
            'sortable'  => false,
        ));

        $this->addColumn('mode', array(
            'header'    => Mage::helper('index')->__('Mode'),
            'width'     => '150',
            'align'     => 'left',
            'index'     => 'mode',
            'type'      => 'options',
            'options'   => $this->_processModel->getModesOptions()
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('index')->__('Status'),
            'width'     => '120',
            'align'     => 'left',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => $this->_processModel->getStatusesOptions(),
            'frame_callback' => array($this, 'decorateStatus')
        ));

        $this->addColumn('ended_at', array(
            'header'    => Mage::helper('index')->__('Last Run'),
            'type'      => 'datetime',
            'width'     => '180',
            'align'     => 'left',
            'index'     => 'ended_at',
            'frame_callback' => array($this, 'decorateDate')
        ));

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('index')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('index')->__('Reindex Data'),
                        'url'       => array('base'=> '*/*/reindexProcess'),
                        'field'     => 'process'
                    ),
//                    array(
//                        'caption'   => Mage::helper('index')->__('Pending Events'),
//                        'url'       => array('base'=> '*/*/reindexEvents'),
//                        'field'     => 'process'
//                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true,
        ));

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
        switch ($row->getStatus()) {
            case Mage_Index_Model_Process::STATUS_PENDING :
                $class = 'grid-severity-notice';
                break;
            case Mage_Index_Model_Process::STATUS_RUNNING :
                $class = 'grid-severity-major';
                break;
            case Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX :
                $class = 'grid-severity-critical';
                break;
        }
        return '<span class="'.$class.'"><span>'.$value.'</span></span>';
    }

    /**
     * Decorate last run date coumn
     *
     * @return string
     */
    public function decorateDate($value, $row, $column, $isExport)
    {
        if(!$value) {
            return $this->__('Never');
        }
        return $value;
    }

    /**
     * Get row edit url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('process'=>$row->getId()));
    }

    /**
     * Add mass-actions to grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('process_id');
        $this->getMassactionBlock()->setFormFieldName('process');

        $modeOptions = Mage::getModel('index/process')->getModesOptions();

        $this->getMassactionBlock()->addItem('change_mode', array(
            'label'         => Mage::helper('index')->__('Change Index Mode'),
            'url'           => $this->getUrl('*/*/massChangeMode'),
            'additional'    => array(
                'mode'      => array(
                    'name'      => 'index_mode',
                    'type'      => 'select',
                    'class'     => 'required-entry',
                    'label'     => Mage::helper('index')->__('Index mode'),
                    'values'    => $modeOptions
                )
            )
        ));

        $this->getMassactionBlock()->addItem('reindex', array(
            'label'    => Mage::helper('index')->__('Reindex Data'),
            'url'      => $this->getUrl('*/*/massReindex'),
            'selected' => true,
        ));

        return $this;
    }
}
