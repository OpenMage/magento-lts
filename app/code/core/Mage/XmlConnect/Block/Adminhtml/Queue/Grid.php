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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect AirMail message queue grid
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Queue_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Setting grid_id, sort order and sort direction
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('app_queue_grid');
        $this->setDefaultSort('exec_time');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Setting collection to show
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('xmlconnect/queue')->getCollection();

        $collection->addFieldToFilter(
            'main_table.status', array('neq' => Mage_XmlConnect_Model_Queue::STATUS_DELETED)
        );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('queue_id', array(
            'header'    => $this->__('ID'),
            'align'     => 'center',
            'index'     => 'main_table.queue_id',
            'width'     => '40px',
            'renderer'  => 'xmlconnect/adminhtml_queue_grid_renderer_id'
        ));

        $this->addColumn('exec_time', array(
            'header'    => $this->__('Queue Date'),
            'index'     => 'exec_time',
            'type'      => 'datetime',
            'gmtoffset' => false,
            'default'   => ' ---- '
        ));

        $this->addColumn('app_code', array(
            'header'    => $this->__('Application Name'),
            'align'     => 'left',
            'index'     => 'app.code',
            'type'      => 'options',
            'options'   => Mage::helper('xmlconnect')->getApplications(),
            'renderer'  => 'xmlconnect/adminhtml_queue_grid_renderer_application'
        ));

        $this->addColumn('name', array(
            'header'    => $this->__('Template Name'),
            'align'     => 'left',
            'index'     => 't.name',
            'type'      => 'text',
            'default'   => '--- Parent template has been deleted ---',
            'renderer'  => 'xmlconnect/adminhtml_queue_grid_renderer_template'
        ));

        $this->addColumn('push_title', array(
            'header'    => $this->__('Push Title'),
            'align'     => 'left',
            'index'     => 'main_table.push_title',
            'type'      => 'text',
            'renderer'  => 'xmlconnect/adminhtml_queue_grid_renderer_pushtitle'
        ));

        $this->addColumn('message_title', array(
            'header'    => $this->__('Message Title'),
            'align'     => 'left',
            'index'     => 'main_table.message_title',
            'type'      => 'text',
            'renderer'  => 'xmlconnect/adminhtml_queue_grid_renderer_msgtitle'
        ));

        $this->addColumn('status', array(
            'header'    => $this->__('Status'),
            'align'     => 'left',
            'index'     => 'main_table.status',
            'type'      => 'options',
            'width'     => '50px',
            'options'   => array(
                Mage_XmlConnect_Model_Queue::STATUS_CANCELED => $this->__('Canceled'),
                Mage_XmlConnect_Model_Queue::STATUS_IN_QUEUE => $this->__('In Queue'),
                Mage_XmlConnect_Model_Queue::STATUS_COMPLETED => $this->__('Completed'),
            ),
            'renderer'  => 'xmlconnect/adminhtml_queue_grid_renderer_status',
        ));

        $this->addColumn('action', array(
            'header'    => $this->__('Action'),
            'type'      => 'action',
            'getter'    => 'getId',
            'renderer'  => 'xmlconnect/adminhtml_queue_grid_renderer_action',

            'filter'    => false,
            'sortable'  => false,
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare mass actions
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('queue');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => $this->__('Delete'),
             'url'      => $this->getUrl('*/*/massDeleteQueue'),
             'confirm'  => $this->__('Are you sure you want to delete selected records?')
        ));

        $this->getMassactionBlock()->addItem('cancel', array(
             'label'    => $this->__('Cancel'),
             'url'      => $this->getUrl('*/*/massCancelQueue'),
             'confirm'  => $this->__('Are you sure you want to cancel selected records?')
        ));
        return $this;
    }

    /**
     * Configure row click url
     *
     * @param Mage_Catalog_Model_Queue|Varien_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/editQueue', array('id' => $row->getId()));
    }
}
