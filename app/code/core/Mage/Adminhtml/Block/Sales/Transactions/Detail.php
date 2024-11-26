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
 * Adminhtml transaction detail
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Transactions_Detail extends Mage_Adminhtml_Block_Widget_Container
{
    /**
     * Transaction model
     *
     * @var Mage_Sales_Model_Order_Payment_Transaction
     */
    protected $_txn;

    /**
     * Add control buttons
     */
    public function __construct()
    {
        parent::__construct();

        $this->_txn = Mage::registry('current_transaction');

        $backUrl = ($this->_txn->getOrderUrl()) ? $this->_txn->getOrderUrl() : $this->getUrl('*/*/');
        $this->_addButton('back', [
            'label'   => Mage::helper('sales')->__('Back'),
            'onclick' => Mage::helper('core/js')->getSetLocationJs($backUrl),
            'class'   => 'back'
        ]);

        if (Mage::getSingleton('admin/session')->isAllowed('sales/transactions/fetch')
            && $this->_txn->getOrderPaymentObject()->getMethodInstance()->canFetchTransactionInfo()
        ) {
            $this->_addButton('fetch', [
                'label'   => Mage::helper('sales')->__('Fetch'),
                'onclick' => Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/*/fetch', ['_current' => true])),
                'class'   => 'button'
            ]);
        }
    }

    /**
     * Retrieve header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__(
            'Transaction # %s | %s',
            $this->_txn->getTxnId(),
            $this->formatDate(
                $this->_txn->getCreatedAt(),
                Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM,
                true
            )
        );
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $this->setTxnIdHtml(Mage::helper('adminhtml/sales')->escapeHtmlWithLinks(
            $this->_txn->getHtmlTxnId(),
            ['a']
        ));

        $this->setParentTxnIdUrlHtml(
            $this->escapeHtml($this->getUrl('*/sales_transactions/view', ['txn_id' => $this->_txn->getParentId()]))
        );

        $this->setParentTxnIdHtml(
            $this->escapeHtml($this->_txn->getParentTxnId())
        );

        $this->setOrderIncrementIdHtml($this->escapeHtml($this->_txn->getOrder()->getIncrementId()));

        $this->setTxnTypeHtml($this->escapeHtml($this->_txn->getTxnType()));

        $this->setOrderIdUrlHtml(
            $this->escapeHtml($this->getUrl('*/sales_order/view', ['order_id' => $this->_txn->getOrderId()]))
        );

        $this->setIsClosedHtml(
            ($this->_txn->getIsClosed()) ? Mage::helper('sales')->__('Yes') : Mage::helper('sales')->__('No')
        );

        $createdAt = (strtotime($this->_txn->getCreatedAt()))
            ? $this->formatDate($this->_txn->getCreatedAt(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true)
            : $this->__('N/A');
        $this->setCreatedAtHtml($this->escapeHtml($createdAt));

        return parent::_toHtml();
    }
}
