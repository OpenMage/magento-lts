<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

use Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract as MassAction;

/**
 * Adminhtml AdminNotification inbox grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Notification_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        $this->setSaveParametersInSession(true);
        $this->setId('notificationGrid');
        $this->setIdFieldName('notification_id');
        $this->setDefaultSort('date_added');
        $this->setDefaultDir('desc');
        $this->setFilterVisibility(false);
    }

    /**
     * @inheritDoc
     * @throws Mage_Core_Exception
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('adminnotification/inbox')
            ->getCollection()
            ->addRemoveFilter();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('severity', [
            'header'    => Mage::helper('adminnotification')->__('Severity'),
            'width'     => '60px',
            'index'     => 'severity',
            'renderer'  => 'adminhtml/notification_grid_renderer_severity',
        ]);

        $this->addColumn('date_added', [
            'header'    => Mage::helper('adminnotification')->__('Date Added'),
            'index'     => 'date_added',
            'type'      => 'datetime',
        ]);

        $this->addColumn('title', [
            'header'    => Mage::helper('adminnotification')->__('Message'),
            'index'     => 'title',
            'renderer'  => 'adminhtml/notification_grid_renderer_notice',
        ]);

        $this->addColumn('actions', [
            'header'    => Mage::helper('adminnotification')->__('Actions'),
            'width'     => '250px',
            'sortable'  => false,
            'renderer'  => 'adminhtml/notification_grid_renderer_actions',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Prepare mass action
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('notification_id');
        $this->getMassactionBlock()->setFormFieldName('notification');

        $this->getMassactionBlock()->addItem(MassAction::MARK_AS_READ, [
            'label'    => Mage::helper('adminnotification')->__('Mark as Read'),
            'url'      => $this->getUrl('*/*/massMarkAsRead', ['_current' => true]),
        ]);

        $this->getMassactionBlock()->addItem(MassAction::REMOVE, [
            'label'    => Mage::helper('adminnotification')->__('Remove'),
            'url'      => $this->getUrl('*/*/massRemove'),
        ]);

        return $this;
    }

    /**
     * @param Mage_AdminNotification_Model_Inbox $row
     * @return string
     */
    public function getRowClass(Varien_Object $row)
    {
        return $row->getIsRead() ? 'read' : 'unread';
    }

    /**
     * @return false
     */
    public function getRowClickCallback()
    {
        return false;
    }
}
