<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml customer grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Online_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize Grid block
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('onlineGrid');
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('last_activity');
        $this->setDefaultDir('DESC');
    }

    /**
     * Prepare collection for grid
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        /** @var Mage_Log_Model_Resource_Visitor_Online_Collection $collection */
        $collection = Mage::getModel('log/visitor_online')
            ->prepare()
            ->getCollection();
        $collection->addCustomerData();

        $this->setCollection($collection);
        parent::_prepareCollection();

        return $this;
    }

    /**
     * Prepare columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('customer_id', [
            'header'    => Mage::helper('customer')->__('ID'),
            'width'     => '40px',
            'align'     => 'right',
            'type'      => 'number',
            'default'   => Mage::helper('customer')->__('n/a'),
            'index'     => 'customer_id'
        ]);

        $this->addColumn('firstname', [
            'header'    => Mage::helper('customer')->__('First Name'),
            'default'   => Mage::helper('customer')->__('Guest'),
            'index'     => 'customer_firstname'
        ]);

        $this->addColumn('middlename', [
            'header'    => Mage::helper('customer')->__('Middle Name'),
            'default'   => Mage::helper('customer')->__('n/a'),
            'index'     => 'customer_middlename'
        ]);

        $this->addColumn('lastname', [
            'header'    => Mage::helper('customer')->__('Last Name'),
            'default'   => Mage::helper('customer')->__('n/a'),
            'index'     => 'customer_lastname'
        ]);

        $this->addColumn('email', [
            'header'    => Mage::helper('customer')->__('Email'),
            'default'   => Mage::helper('customer')->__('n/a'),
            'index'     => 'customer_email'
        ]);

        $this->addColumn('ip_address', [
            'header'    => Mage::helper('customer')->__('IP Address'),
            'default'   => Mage::helper('customer')->__('n/a'),
            'index'     => 'remote_addr',
            'renderer'  => 'adminhtml/customer_online_grid_renderer_ip',
            'filter'    => false,
            'sort'      => false
        ]);

        $this->addColumn('session_start_time', [
            'header'    => Mage::helper('customer')->__('Session Start Time'),
            'align'     => 'left',
            'type'      => 'datetime',
            'default'   => Mage::helper('customer')->__('n/a'),
            'index'     => 'first_visit_at'
        ]);

        $this->addColumn('last_activity', [
            'header'    => Mage::helper('customer')->__('Last Activity'),
            'align'     => 'left',
            'type'      => 'datetime',
            'default'   => Mage::helper('customer')->__('n/a'),
            'index'     => 'last_visit_at'
        ]);

        $typeOptions = [
            Mage_Log_Model_Visitor::VISITOR_TYPE_CUSTOMER => Mage::helper('customer')->__('Customer'),
            Mage_Log_Model_Visitor::VISITOR_TYPE_VISITOR  => Mage::helper('customer')->__('Visitor'),
        ];

        $this->addColumn('type', [
            'header'    => Mage::helper('customer')->__('Type'),
            'type'      => 'options',
            'options'   => $typeOptions,
            'index'     => 'visitor_type'
        ]);

        $this->addColumn('last_url', [
            'header'    => Mage::helper('customer')->__('Last URL'),
            'type'      => 'wrapline',
            'lineLength' => '60',
            'default'   => Mage::helper('customer')->__('n/a'),
            'renderer'  => 'adminhtml/customer_online_grid_renderer_url',
            'index'     => 'last_url'
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Retrieve Row URL
     *
     * @param Mage_Core_Model_Abstract $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return (Mage::getSingleton('admin/session')->isAllowed('customer/manage') && $row->getCustomerId())
            ? $this->getUrl('*/customer/edit', ['id' => $row->getCustomerId()]) : '';
    }
}
