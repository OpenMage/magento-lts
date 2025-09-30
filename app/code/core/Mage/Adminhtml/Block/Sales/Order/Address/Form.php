<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml sales order edit address block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Address_Form extends Mage_Adminhtml_Block_Sales_Order_Create_Form_Address
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('sales/order/address/form.phtml');
    }

    /**
     * Order address getter
     *
     * @return Mage_Sales_Model_Order_Address
     */
    protected function _getAddress()
    {
        return Mage::registry('order_address');
    }

    /**
     * Define form attributes (id, method, action)
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $this->_form->setId('edit_form');
        $this->_form->setMethod('post');
        $this->_form->setAction($this->getUrl('*/*/addressSave', ['address_id' => $this->_getAddress()->getId()]));
        $this->_form->setUseContainer(true);
        return $this;
    }

    /**
     * Form header text getter
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Order Address Information');
    }

    /**
     * Return Form Elements values
     *
     * @return array
     */
    public function getFormValues()
    {
        return $this->_getAddress()->getData();
    }
}
