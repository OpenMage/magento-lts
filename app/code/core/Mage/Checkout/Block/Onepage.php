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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Onepage checkout block
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Block_Onepage extends Mage_Checkout_Block_Onepage_Abstract
{
    public function getSteps()
    {
        $steps = array();

        if (!$this->isCustomerLoggedIn()) {
            $steps['login'] = $this->getCheckout()->getStepData('login');
        }

        $stepCodes = array('billing', 'shipping', 'shipping_method', 'payment', 'review');

        foreach ($stepCodes as $step) {
            $steps[$step] = $this->getCheckout()->getStepData($step);
        }
        return $steps;
    }

    public function getActiveStep()
    {
        return $this->isCustomerLoggedIn() ? 'billing' : 'login';
    }

/*
    // ADDRESSES

    public function getAddressesHtmlSelect($address, $type)
    {
        if ($this->isCustomerLoggedIn()) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $a) {
                $options[] = array(
                    'value'=>$a->getId(),
                    'label'=>$a->getStreet(-1).', '.$a->getCity().', '.$a->getRegion().' '.$a->getPostcode(),
                );
            }

            $addressId = $address->getId();
            if (empty($addressId)) {
                if ($type=='billing') {
                    $address = $this->getCustomer()->getPrimaryBillingAddress();
                } else {
                    $address = $this->getCustomer()->getPrimaryShippingAddress();
                }
                if ($address) {
                    $addressId = $address->getId();
                }
            }

            $select = $this->getLayout()->createBlock('core/html_select')
                ->setName($type.'_address_id')
                ->setId($type.'-address-select')
                ->setExtraParams('onchange="'.$type.'.newAddress(!this.value)"')
                ->setValue($addressId)
                ->setOptions($options);

            $select->addOption('', 'New Address');

            return $select->getHtml();
        }
        return '';
    }

    public function getCountryHtmlSelect($address, $type)
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'[country_id]')
            ->setId($type.':country_id')
            ->setTitle(Mage::helper('checkout')->__('Country'))
            ->setClass('validate-select')
            ->setValue($address->getCountryId())
            ->setOptions($this->getCountryCollection()->toOptionArray());

        if ($type==='shipping') {
            $select->setExtraParams('onchange="shipping.setSameAsBilling(false);"');
        }

        return $select->getHtml();
    }


    public function getRegionHtmlSelect($address, $type)
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($type.'[region]')
            ->setId($type.':region')
            ->setTitle(Mage::helper('checkout')->__('State/Province'))
            ->setClass('required-entry validate-state')
            ->setValue($address->getRegionId())
            ->setOptions($this->getRegionCollection()->toOptionArray());

        return $select->getHtml();
    }

    // LOGIN STEP

    public function getMessages()
    {
        return Mage::getSingleton('customer/session')->getMessages(true);
    }

    public function getLoginPostAction()
    {
        return Mage::getUrl('customer/account/loginPost', array('_secure'=>true));
    }

    public function getSuccessUrl()
    {
        return $this->getUrl('* /*');////////////
    }

    public function getErrorUrl()
    {
        return $this->getUrl('* /*');////////////
    }

    public function getMethod()
    {
        return $this->getQuote()->getCheckoutMethod();
    }

    public function getMethodData()
    {
        return $this->getCheckout()->getMethodData();
    }

    // BILLING STEP

    public function getBillingAddress() {
        if (!$this->isCustomerLoggedIn()) {
            return $this->getQuote()->getBillingAddress();
        } else {
            return Mage::getModel('sales/quote_address');
        }
    }

    // SHIPPING STEP

    public function getShippingAddress()
    {
        if (!$this->isCustomerLoggedIn()) {
            return $this->getQuote()->getShippingAddress();
        } else {
            return Mage::getModel('sales/quote_address');
        }
    }

    // PAYMENT STEP

    public function getPayment()
    {
        $payment = $this->getQuote()->getPayment();
        if (empty($payment)) {
            $payment = Mage::getModel('sales/quote_payment');
        } else {
            $payment->setCcNumber(null)->setCcCid(null);
        }
        return $payment;
    }
    */
}
