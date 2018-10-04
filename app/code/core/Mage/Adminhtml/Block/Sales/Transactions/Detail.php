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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml transaction detail
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->_txn = Mage::registry('current_transaction');

        $backUrl = ($this->_txn->getOrderUrl()) ? $this->_txn->getOrderUrl() : $this->getUrl('*/*/');
        $this->_addButton('back', array(
            'label'   => Mage::helper('sales')->__('Back'),
            'onclick' => "setLocation('{$backUrl}')",
            'class'   => 'back'
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/transactions/fetch')
            && $this->_txn->getOrderPaymentObject()->getMethodInstance()->canFetchTransactionInfo()) {
            $fetchUrl = $this->getUrl('*/*/fetch' , array('_current' => true));
            $this->_addButton('fetch', array(
                'label'   => Mage::helper('sales')->__('Fetch'),
                'onclick' => "setLocation('{$fetchUrl}')",
                'class'   => 'button'
            ));
        }
    }

    /**
     * Retrieve header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__("Transaction # %s | %s", $this->_txn->getTxnId(), $this->formatDate($this->_txn->getCreatedAt(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true));
    }

    protected function _toHtml()
    {
        $this->setTxnIdHtml(Mage::helper('adminhtml/sales')->escapeHtmlWithLinks(
            $this->_txn->getHtmlTxnId(),
            array('a')
        ));

        $this->setParentTxnIdUrlHtml(
            $this->escapeHtml($this->getUrl('*/sales_transactions/view', array('txn_id' => $this->_txn->getParentId())))
        );

        $this->setParentTxnIdHtml(
            $this->escapeHtml($this->_txn->getParentTxnId())
        );

        $this->setOrderIncrementIdHtml($this->escapeHtml($this->_txn->getOrder()->getIncrementId()));

        $this->setTxnTypeHtml($this->escapeHtml($this->_txn->getTxnType()));

        $this->setOrderIdUrlHtml(
            $this->escapeHtml($this->getUrl('*/sales_order/view', array('order_id' => $this->_txn->getOrderId())))
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
