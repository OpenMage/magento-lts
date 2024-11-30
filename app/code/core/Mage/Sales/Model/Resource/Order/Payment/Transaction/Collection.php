<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payment transactions collection
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order_Payment_Transaction_Collection extends Mage_Sales_Model_Resource_Order_Collection_Abstract
{
    /**
     * Order ID filter
     *
     * @var int
     */
    protected $_orderId                = null;

    /**
     * Columns of order info that should be selected
     *
     * @var array
     */
    protected $_addOrderInformation    = [];

    /**
     * Columns of payment info that should be selected
     *
     * @var array
     */
    protected $_addPaymentInformation  = [];

    /**
     * Order Store ids
     *
     * @var array
     */
    protected $_storeIds               = [];

    /**
     * Payment ID filter
     *
     * @var int
     */
    protected $_paymentId              = null;

    /**
     * Parent ID filter
     *
     * @var int
     */
    protected $_parentId               = null;

    /**
     * Filter by transaction type
     *
     * @var array
     */
    protected $_txnTypes               = null;

    /**
     * Order field for setOrderFilter
     *
     * @var string
     */
    protected $_orderField             = 'order_id';

    /**
     * Initialize collection items factory class
     */
    protected function _construct()
    {
        $this->_init('sales/order_payment_transaction');
        parent::_construct();
    }

    /**
     * Join order information
     *
     * @return $this
     */
    public function addOrderInformation(array $keys)
    {
        $this->_addOrderInformation = array_merge($this->_addOrderInformation, $keys);
        $this->addFilterToMap('created_at', 'main_table.created_at');
        return $this;
    }

    /**
     * Join payment information
     *
     * @return $this
     */
    public function addPaymentInformation(array $keys)
    {
        $this->_addPaymentInformation = array_merge($this->_addPaymentInformation, $keys);
        return $this;
    }

    /**
     * Order ID filter setter
     *
     * @param int $orderId
     * @return $this
     */
    public function addOrderIdFilter($orderId)
    {
        $this->_orderId = (int)$orderId;
        return $this;
    }

    /**
     * Payment ID filter setter
     * Can take either the integer id or the payment instance
     *
     * @param Mage_Sales_Model_Order_Payment|int $payment
     * @return $this
     */
    public function addPaymentIdFilter($payment)
    {
        $id = $payment;
        if (is_object($payment)) {
            $id = $payment->getId();
        }
        $this->_paymentId = (int)$id;
        return $this;
    }

    /**
     * Parent ID filter setter
     *
     * @param int $parentId
     * @return $this
     */
    public function addParentIdFilter($parentId)
    {
        $this->_parentId = (int)$parentId;
        return $this;
    }

    /**
     * Transaction type filter setter
     *
     * @param array|string $txnType
     * @return $this
     */
    public function addTxnTypeFilter($txnType)
    {
        if (!is_array($txnType)) {
            $txnType = [$txnType];
        }
        $this->_txnTypes = $txnType;
        return $this;
    }

    /**
     * Add filter by store ids
     *
     * @param int|array $storeIds
     * @return $this
     */
    public function addStoreFilter($storeIds)
    {
        $storeIds = (is_array($storeIds)) ? $storeIds : [$storeIds];
        $this->_storeIds = array_merge($this->_storeIds, $storeIds);
        return $this;
    }

    /**
     * Prepare filters
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        parent::_beforeLoad();

        if ($this->isLoaded()) {
            return $this;
        }

        // filters
        if ($this->_paymentId) {
            $this->getSelect()->where('main_table.payment_id = ?', $this->_paymentId);
        }
        if ($this->_parentId) {
            $this->getSelect()->where('main_table.parent_id = ?', $this->_parentId);
        }
        if ($this->_txnTypes) {
            $this->getSelect()->where('main_table.txn_type IN(?)', $this->_txnTypes);
        }
        if ($this->_orderId) {
            $this->getSelect()->where('main_table.order_id = ?', $this->_orderId);
        }
        if ($this->_addPaymentInformation) {
            $this->getSelect()->joinInner(
                ['sop' => $this->getTable('sales/order_payment')],
                'main_table.payment_id = sop.entity_id',
                $this->_addPaymentInformation
            );
        }
        if ($this->_storeIds) {
            $this->getSelect()->where('so.store_id IN(?)', $this->_storeIds);
            $this->addOrderInformation(['store_id']);
        }
        if ($this->_addOrderInformation) {
            $this->getSelect()->joinInner(
                ['so' => $this->getTable('sales/order')],
                'main_table.order_id = so.entity_id',
                $this->_addOrderInformation
            );
        }
        return $this;
    }

    /**
     * Unserialize additional_information in each item
     *
     * @inheritDoc
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $this->getResource()->unserializeFields($item);
        }
        return parent::_afterLoad();
    }
}
