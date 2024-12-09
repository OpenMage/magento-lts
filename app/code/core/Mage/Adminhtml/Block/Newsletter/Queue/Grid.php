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

/**
 * Adminhtml newsletter queue grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Newsletter_Queue_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('queueGrid');
        $this->setDefaultSort('start_at');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('newsletter/queue_collection')
            ->addSubscribersInfo();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('queue_id', [
            'header'    =>  Mage::helper('newsletter')->__('ID'),
            'index'     =>  'queue_id',
            'width'     =>  10
        ]);

        $this->addColumn('start_at', [
            'header'    =>  Mage::helper('newsletter')->__('Queue Start'),
            'type'      =>  'datetime',
            'index'     =>  'queue_start_at',
            'gmtoffset' => true,
            'default'   =>  ' ---- '
        ]);

        $this->addColumn('finish_at', [
            'header'    =>  Mage::helper('newsletter')->__('Queue Finish'),
            'type'      =>  'datetime',
            'index'     =>  'queue_finish_at',
            'gmtoffset' => true,
            'default'   =>  ' ---- '
        ]);

        $this->addColumn('newsletter_subject', [
            'header'    =>  Mage::helper('newsletter')->__('Subject'),
            'index'     =>  'newsletter_subject'
        ]);

        $this->addColumn('status', [
            'header'    => Mage::helper('newsletter')->__('Status'),
            'index'     => 'queue_status',
            'type'      => 'options',
            'options'   => [
                Mage_Newsletter_Model_Queue::STATUS_SENT    => Mage::helper('newsletter')->__('Sent'),
                Mage_Newsletter_Model_Queue::STATUS_CANCEL  => Mage::helper('newsletter')->__('Cancelled'),
                Mage_Newsletter_Model_Queue::STATUS_NEVER   => Mage::helper('newsletter')->__('Not Sent'),
                Mage_Newsletter_Model_Queue::STATUS_SENDING => Mage::helper('newsletter')->__('Sending'),
                Mage_Newsletter_Model_Queue::STATUS_PAUSE   => Mage::helper('newsletter')->__('Paused'),
            ],
            'width'     => '100px',
         ]);

        $this->addColumn('subscribers_sent', [
            'header'    =>  Mage::helper('newsletter')->__('Processed'),
               'type'       => 'number',
            'index'     => 'subscribers_sent'
        ]);

        $this->addColumn('subscribers_total', [
            'header'    =>  Mage::helper('newsletter')->__('Recipients'),
            'type'      => 'number',
            'index'     => 'subscribers_total'
        ]);

        $this->addColumn('action', [
            'type'      => 'action',
            'no_link'   => true,
            'width'     => '100',
            'renderer'  =>  'adminhtml/newsletter_queue_grid_renderer_action'
        ]);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
