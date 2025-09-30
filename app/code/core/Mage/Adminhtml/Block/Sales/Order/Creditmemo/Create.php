<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml creditmemo create
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order_creditmemo';
        $this->_mode = 'create';

        parent::__construct();

        $this->_removeButton('delete');
        $this->_removeButton('save');
    }

    /**
     * Retrieve creditmemo model instance
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function getCreditmemo()
    {
        return Mage::registry('current_creditmemo');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getCreditmemo()->getInvoice()) {
            $header = Mage::helper('sales')->__(
                'New Credit Memo for Invoice #%s',
                $this->escapeHtml($this->getCreditmemo()->getInvoice()->getIncrementId()),
            );
        } else {
            $header = Mage::helper('sales')->__(
                'New Credit Memo for Order #%s',
                $this->escapeHtml($this->getCreditmemo()->getOrder()->getRealOrderId()),
            );
        }

        return $header;
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/sales_order/view', ['order_id' => $this->getCreditmemo()->getOrderId()]);
    }
}
