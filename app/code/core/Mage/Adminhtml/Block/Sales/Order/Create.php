<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */


/**
 * Adminhtml sales order create
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order';
        $this->_mode = 'create';

        parent::__construct();

        $this->setId('sales_order_create');

        $customerId = $this->_getSession()->getCustomerId();
        $storeId    = $this->_getSession()->getStoreId();

        $this->_updateButton(self::BUTTON_TYPE_SAVE, 'label', Mage::helper('sales')->__('Submit Order'));
        $this->_updateButton(self::BUTTON_TYPE_SAVE, 'onclick', 'order.submit()');
        $this->_updateButton(self::BUTTON_TYPE_SAVE, 'id', 'submit_order_top_button');
        if (is_null($customerId) || !$storeId) {
            $this->_updateButton(self::BUTTON_TYPE_SAVE, 'style', 'display:none');
        }

        $this->_updateButton(self::BUTTON_TYPE_BACK, 'id', 'back_order_top_button');
        $this->_updateButton(self::BUTTON_TYPE_BACK, 'onclick', Mage::helper('core/js')->getSetLocationJs($this->getBackUrl()));

        $this->_updateButton(self::BUTTON_TYPE_RESET, 'id', 'reset_order_top_button');

        if (!$this->_isCanCancel() || is_null($customerId)) {
            $this->_updateButton(self::BUTTON_TYPE_RESET, 'style', 'display:none');
        } else {
            $this->_updateButton(self::BUTTON_TYPE_BACK, 'style', 'display:none');
        }

        $this->_updateButton(self::BUTTON_TYPE_RESET, 'label', Mage::helper('sales')->__('Cancel'));
        $this->_updateButton(self::BUTTON_TYPE_RESET, 'class', 'cancel');
        $this->_updateButton(
            self::BUTTON_TYPE_RESET,
            'onclick',
            Mage::helper('core/js')->getDeleteConfirmJs(
                $this->getCancelUrl(),
                Mage::helper('sales')->__('Are you sure you want to cancel this order?'),
            ),
        );
    }

    /**
     * Check access for cancel action
     *
     * @return bool
     */
    protected function _isCanCancel()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel');
    }

    /**
     * Prepare header html
     *
     * @return string
     */
    #[Override]
    public function getHeaderHtml()
    {
        return '<div id="order-header">'
            . $this->getLayout()->createBlock('adminhtml/sales_order_create_header')->toHtml()
            . '</div>';
    }

    /**
     * Prepare form html. Add block for configurable product modification interface
     *
     * @return string
     */
    #[Override]
    public function getFormHtml()
    {
        $html = parent::getFormHtml();
        return $html . $this->getLayout()->createBlock('adminhtml/catalog_product_composite_configure')->toHtml();
    }

    /**
     * @return string
     */
    #[Override]
    public function getHeaderWidth()
    {
        return 'width: 70%;';
    }

    /**
     * Retrieve quote session object
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    /**
     * @return string
     */
    public function getCancelUrl()
    {
        if ($this->_getSession()->getOrder()->getId()) {
            return $this->getUrl('*/sales_order/view', [
                'order_id' => Mage::getSingleton('adminhtml/session_quote')->getOrder()->getId(),
            ]);
        }

        return $this->getUrl('*/*/cancel');
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    #[Override]
    public function getBackUrl()
    {
        return $this->getUrl('*/' . $this->_controller . '/');
    }
}
