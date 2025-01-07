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
 * Adminhtml transactions grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 *
 * @method Mage_Sales_Model_Resource_Order_Payment_Transaction_Collection getCollection()
 */
class Mage_Adminhtml_Block_Sales_Transactions_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('order_transactions');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare collection for grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->getCollection() ?: Mage::getResourceModel('sales/order_payment_transaction_collection');
        $order = Mage::registry('current_order');
        if ($order) {
            $collection->addOrderIdFilter($order->getId());
        }
        $collection->addOrderInformation(['increment_id']);
        $collection->addPaymentInformation(['method']);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('transaction_id', [
            'header'    => Mage::helper('sales')->__('ID #'),
            'index'     => 'transaction_id',
            'type'      => 'number',
        ]);

        $this->addColumn('increment_id', [
            'header'    => Mage::helper('sales')->__('Order ID'),
            'index'     => 'increment_id',
            'type'      => 'text',
            'escape'    => true,
        ]);

        $this->addColumn('txn_id', [
            'header'    => Mage::helper('sales')->__('Transaction ID'),
            'index'     => 'txn_id',
            'type'      => 'text',
        ]);

        $this->addColumn('parent_txn_id', [
            'header'    => Mage::helper('sales')->__('Parent Transaction ID'),
            'index'     => 'parent_txn_id',
            'type'      => 'text',
        ]);

        $this->addColumn('method', [
            'header'    => Mage::helper('sales')->__('Payment Method Name'),
            'index'     => 'method',
            'type'      => 'options',
            'options'       => Mage::helper('payment')->getPaymentMethodList(true),
            'option_groups' => Mage::helper('payment')->getPaymentMethodList(true, true, true),
        ]);

        $this->addColumn('txn_type', [
            'header'    => Mage::helper('sales')->__('Transaction Type'),
            'index'     => 'txn_type',
            'type'      => 'options',
            'options'   => Mage::getSingleton('sales/order_payment_transaction')->getTransactionTypes(),
        ]);

        $this->addColumn('is_closed', [
            'header'    => Mage::helper('sales')->__('Is Closed'),
            'index'     => 'is_closed',
            'width'     => 1,
            'type'      => 'options',
            'align'     => 'center',
            'options'   => [
                1  => Mage::helper('sales')->__('Yes'),
                0  => Mage::helper('sales')->__('No'),
            ],
        ]);

        $this->addColumn('created_at', [
            'header'    => Mage::helper('sales')->__('Created At'),
            'index'     => 'created_at',
            'type'      => 'datetime',
            'align'     => 'center',
            'default'   => $this->__('N/A'),
            'html_decorators' => ['nobr'],
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Retrieve grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * Retrieve row url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', ['txn_id' => $row->getId()]);
    }
}
