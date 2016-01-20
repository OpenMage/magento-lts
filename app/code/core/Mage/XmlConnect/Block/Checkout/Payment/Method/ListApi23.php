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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page checkout payment methods xml renderer. API version 23
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Payment_Method_ListApi23 extends Mage_XmlConnect_Block_Checkout_Payment_Method_List
{
    /**
     * Method list xml object
     *
     * @var Mage_XmlConnect_Model_Simplexml_Element
     */
    protected $_methodList;

    /**
     * Add gift card info to xml and check is covered a quote. API version 23
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $methodsXmlObj
     * @return bool
     */
    protected function _addGiftCard(Mage_XmlConnect_Model_Simplexml_Element $methodsXmlObj) {
        if (is_object(Mage::getConfig()->getNode('modules/Enterprise_GiftCardAccount'))) {
            $this->addGiftCardToXmlObj($methodsXmlObj);
            if ($this->getIsUsedGiftCard() && $this->_isPaymentRequired()) {
                $this->_addFreePaymentToXmlObj($this->getMethodList($methodsXmlObj));
                return true;
            }
        }
        return false;
    }

    /**
     * Add gift card details to XML object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $methodsXmlObj
     * @return null
     */
    public function addGiftCardToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $methodsXmlObj)
    {
        $giftCardInfoBlock = $this->getLayout()->addBlock(
            'enterprise_giftcardaccount/checkout_onepage_payment_additional', 'giftcard_info'
        );

        if (intval($giftCardInfoBlock->getAppliedGiftCardAmount())) {
            $amount = $this->getQuote()->getStore()->formatPrice($giftCardInfoBlock->getAppliedGiftCardAmount(), false);
            $amount = $this->__('Gift Card amount applied to order: %s', $amount);
            $methodsXmlObj->addCustomChild('information', null, array('label' => $amount));
            $this->setIsUsedGiftCard(true);
        }
    }

    /**
     * Add customer balance details to XML object. API version 23
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $methodsXmlObj
     * @return Mage_XmlConnect_Block_Checkout_Payment_Method_ListApi23
     */
    public function addCustomerBalanceToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $methodsXmlObj)
    {
        $methodsXmlObj = $this->getMethodList($methodsXmlObj);
        if (is_object(Mage::getConfig()->getNode('modules/Enterprise_CustomerBalance'))) {
            /** @var $customerBalanceBlock Enterprise_CustomerBalance_Block_Checkout_Onepage_Payment_Additional */
            $customerBalanceBlock = $this->getLayout()
                ->addBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customer_balance');
            $storeCreditFlag = (int) Mage::getStoreConfig(Enterprise_CustomerBalance_Helper_Data::XML_PATH_ENABLED);
            if ($storeCreditFlag && $customerBalanceBlock->isDisplayContainer()) {
                $balance = $this->getQuote()->getStore()->formatPrice($customerBalanceBlock->getBalance(), false);
                $methodsXmlObj->addCustomChild('method', null, array(
                    'id'        => 'customer_balance',
                    'post_name' => 'payment[use_customer_balance]',
                    'code'      => 1,
                    'label'     => $this->__('Use Store Credit (%s available)', $balance),
                    'is_cover_a_quote' => intval($customerBalanceBlock->isFullyPaidAfterApplication()),
                    'selected'  => intval($customerBalanceBlock->isCustomerBalanceUsed())
                ));
            }
        }
        return $this;
    }

    /**
     * Get method list object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $methodsXmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function getMethodList($methodsXmlObj)
    {
        if (null === $this->_methodList) {
            $this->_methodList = $methodsXmlObj->addCustomChild('method_list');
        }
        return $this->_methodList;
    }

    /**
     * Add payment methods info to xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $methodsXmlObj
     * @return Mage_XmlConnect_Block_Checkout_Payment_Method_List
     */
    protected function _buildPaymentMethods(Mage_XmlConnect_Model_Simplexml_Element $methodsXmlObj)
    {
        return parent::_buildPaymentMethods($this->getMethodList($methodsXmlObj));
    }
}
