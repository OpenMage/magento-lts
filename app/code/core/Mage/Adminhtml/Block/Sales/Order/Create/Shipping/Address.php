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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml sales order create shipping address block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Shipping_Address
    extends Mage_Adminhtml_Block_Sales_Order_Create_Form_Address
{
    /**
     * Return header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Shipping Address');
    }

    /**
     * Return Header CSS Class
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-shipping-address';
    }

    /**
     * Prepare Form and add elements to form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $this->setJsVariablePrefix('shippingAddress');
        parent::_prepareForm();

        $this->_form->addFieldNameSuffix('order[shipping_address]');
        $this->_form->setHtmlNamePrefix('order[shipping_address]');
        $this->_form->setHtmlIdPrefix('order-shipping_address_');

        return $this;
    }

    /**
     * Return is shipping address flag
     *
     * @return boolean
     */
    public function getIsShipping()
    {
        return true;
    }

    /**
     * Same as billing address flag
     *
     * @return boolean
     */
    public function getIsAsBilling()
    {
        return $this->getCreateOrderModel()->getShippingAddress()->getSameAsBilling();
    }

    /**
     * Saving shipping address must be turned off, when it is the same as billing
     *
     * @return bool
     */
    public function getDontSaveInAddressBook()
    {
        return $this->getIsAsBilling();
    }

    /**
     * Return Form Elements values
     *
     * @return array
     */
    public function getFormValues()
    {
        return $this->getAddress()->getData();
    }

    /**
     * Return customer address id
     *
     * @return int|boolean
     */
    public function getAddressId()
    {
        return $this->getAddress()->getCustomerAddressId();
    }

    /**
     * Return address object
     *
     * @return Mage_Customer_Model_Address
     */
    public function getAddress()
    {
        if ($this->getIsAsBilling()) {
            $address = $this->getCreateOrderModel()->getBillingAddress();
        } else {
            $address = $this->getCreateOrderModel()->getShippingAddress();
        }
        return $address;
    }

    /**
     * Return is address disabled flag
     * Return true is the quote is virtual
     *
     * @return boolean
     */
    public function getIsDisabled()
    {
        return $this->getQuote()->isVirtual();
    }
}
