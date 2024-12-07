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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

use Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract as MassAction;

/**
 * Adminhtml newsletter subscribers grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Newsletter_Subscriber_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set main configuration of grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('subscriberGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('subscriber_id');
        $this->setDefaultDir('desc');
    }

    /**
     * Prepare collection for grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     * @throws Exception
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceSingleton('newsletter/subscriber_collection');
        /** @var Mage_Newsletter_Model_Resource_Subscriber_Collection $collection */
        $collection
            ->showCustomerInfo()
            ->addSubscriberTypeField()
            ->showStoreInfo();

        if ($this->getRequest()->getParam('queue', false)) {
            $collection->useQueue(Mage::getModel('newsletter/queue')
                ->load($this->getRequest()->getParam('queue')));
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('subscriber_id', [
            'header'    => Mage::helper('newsletter')->__('ID'),
            'index'     => 'subscriber_id',
        ]);

        $this->addColumn('email', [
            'header'    => Mage::helper('newsletter')->__('Email'),
            'index'     => 'subscriber_email',
        ]);

        $this->addColumn('type', [
            'header'    => Mage::helper('newsletter')->__('Type'),
            'index'     => 'type',
            'type'      => 'options',
            'options'   => [
                1  => Mage::helper('newsletter')->__('Guest'),
                2  => Mage::helper('newsletter')->__('Customer'),
            ],
        ]);

        $this->addColumn('firstname', [
            'header'    => Mage::helper('newsletter')->__('Customer First Name'),
            'index'     => 'customer_firstname',
            'default'   =>    '----',
        ]);

        $this->addColumn('middlename', [
            'header'    => Mage::helper('newsletter')->__('Customer Middle Name'),
            'index'     => 'customer_middlename',
            'default'   =>    '----',
        ]);

        $this->addColumn('lastname', [
            'header'    => Mage::helper('newsletter')->__('Customer Last Name'),
            'index'     => 'customer_lastname',
            'default'   =>    '----',
        ]);

        $this->addColumn('status', [
            'header'    => Mage::helper('newsletter')->__('Status'),
            'index'     => 'subscriber_status',
            'type'      => 'options',
            'options'   => [
                Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE   => Mage::helper('newsletter')->__('Not Activated'),
                Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED   => Mage::helper('newsletter')->__('Subscribed'),
                Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED => Mage::helper('newsletter')->__('Unsubscribed'),
                Mage_Newsletter_Model_Subscriber::STATUS_UNCONFIRMED => Mage::helper('newsletter')->__('Unconfirmed'),
            ],
        ]);

        $this->addColumn('website', [
            'header'    => Mage::helper('newsletter')->__('Website'),
            'index'     => 'website_id',
            'type'      => 'options',
            'options'   => $this->_getWebsiteOptions(),
        ]);

        $this->addColumn('group', [
            'header'    => Mage::helper('newsletter')->__('Store'),
            'index'     => 'group_id',
            'type'      => 'options',
            'options'   => $this->_getStoreGroupOptions(),
        ]);

        $this->addColumn('store', [
            'header'    => Mage::helper('newsletter')->__('Store View'),
            'index'     => 'store_id',
            'type'      => 'options',
            'options'   => $this->_getStoreOptions(),
        ]);

        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('Excel XML'));
        return parent::_prepareColumns();
    }

    /**
     * Convert OptionsValue array to Options array
     *
     * @param array $optionsArray
     * @return array
     */
    protected function _getOptions($optionsArray)
    {
        $options = [];
        foreach ($optionsArray as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    /**
     * Retrieve Website Options array
     *
     * @return array
     */
    protected function _getWebsiteOptions()
    {
        return Mage::getModel('adminhtml/system_store')->getWebsiteOptionHash();
    }

    /**
     * Retrieve Store Group Options array
     *
     * @return array
     */
    protected function _getStoreGroupOptions()
    {
        return Mage::getModel('adminhtml/system_store')->getStoreGroupOptionHash();
    }

    /**
     * Retrieve Store Options array
     *
     * @return array
     */
    protected function _getStoreOptions()
    {
        return Mage::getModel('adminhtml/system_store')->getStoreOptionHash();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('subscriber_id');
        $this->getMassactionBlock()->setFormFieldName('subscriber');
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem(MassAction::UNSUBSCRIBE, [
            'label'        => Mage::helper('newsletter')->__('Unsubscribe'),
            'url'          => $this->getUrl('*/*/massUnsubscribe'),
        ]);

        $this->getMassactionBlock()->addItem(MassAction::DELETE, [
            'label'        => Mage::helper('newsletter')->__('Delete'),
            'url'          => $this->getUrl('*/*/massDelete'),
        ]);

        return $this;
    }
}
