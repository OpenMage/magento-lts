<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Multishipping checkout select billing address
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Block_Multishipping_Address_Select extends Mage_Checkout_Block_Multishipping_Abstract
{
    /**
     * @return Mage_Checkout_Block_Multishipping_Abstract
     */
    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(Mage::helper('checkout')->__('Change Billing Address') . ' - ' . $headBlock->getDefaultTitle());
        }
        return parent::_prepareLayout();
    }

    /**
     * @return Mage_Checkout_Model_Type_Multishipping|Mage_Core_Model_Abstract
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/type_multishipping');
    }

    /**
     * @return Mage_Customer_Model_Address[]|mixed
     */
    public function getAddressCollection()
    {
        $collection = $this->getData('address_collection');
        if (is_null($collection)) {
            $collection = $this->_getCheckout()->getCustomer()->getAddresses();
            $this->setData('address_collection', $collection);
        }
        return $collection;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return bool
     */
    public function isAddressDefaultBilling($address)
    {
        return $address->getId() == $this->_getCheckout()->getCustomer()->getDefaultBilling();
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return bool
     */
    public function isAddressDefaultShipping($address)
    {
        return $address->getId() == $this->_getCheckout()->getCustomer()->getDefaultShipping();
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return string
     */
    public function getEditAddressUrl($address)
    {
        return $this->getUrl('*/*/editAddress', ['id'=>$address->getId()]);
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return string
     */
    public function getSetAddressUrl($address)
    {
        return $this->getUrl('*/*/setBilling', ['id'=>$address->getId()]);
    }

    /**
     * @return string
     */
    public function getAddNewUrl()
    {
        return $this->getUrl('*/*/newBilling');
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/multishipping/billing');
    }
}
