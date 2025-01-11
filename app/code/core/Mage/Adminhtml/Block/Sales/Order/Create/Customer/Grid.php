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
 * @copyright  Copyright (c) 2018-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order create block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_customer_grid');
        $this->setRowClickCallback('order.selectCustomer.bind(order)');
        $this->setUseAjax(true);
        $this->setDefaultSort('entity_id');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('billing_regione', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left')
            ->joinField('store_name', 'core/store', 'name', 'store_id=store_id', null, 'left');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', [
            'header'    => Mage::helper('sales')->__('ID'),
            'index'     => 'entity_id',
        ]);
        $this->addColumn('name', [
            'header'    => Mage::helper('sales')->__('Name'),
            'index'     => 'name',
        ]);
        $this->addColumn('email', [
            'header'    => Mage::helper('sales')->__('Email'),
            'width'     => '150px',
            'index'     => 'email',
        ]);
        $this->addColumn('Telephone', [
            'header'    => Mage::helper('sales')->__('Telephone'),
            'width'     => '100px',
            'index'     => 'billing_telephone',
        ]);
        $this->addColumn('billing_postcode', [
            'header'    => Mage::helper('sales')->__('ZIP/Post Code'),
            'width'     => '120px',
            'index'     => 'billing_postcode',
        ]);
        $this->addColumn('billing_country_id', [
            'header'    => Mage::helper('sales')->__('Country'),
            'width'     => '100px',
            'type'      => 'country',
            'index'     => 'billing_country_id',
        ]);
        $this->addColumn('billing_regione', [
            'header'    => Mage::helper('sales')->__('State/Province'),
            'width'     => '100px',
            'index'     => 'billing_regione',
        ]);

        $this->addColumn('store_name', [
            'header'    => Mage::helper('sales')->__('Signed Up From'),
            'align'     => 'center',
            'index'     => 'store_name',
            'width'     => '130px',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @deprecated since 1.1.7
     */
    public function getRowId($row)
    {
        return $row->getId();
    }

    public function getRowUrl($row)
    {
        return $row->getId();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/loadBlock', ['block' => 'customer_grid']);
    }
}
