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
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paypal shortcut link
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_AmazonPayments_Block_Asp_Shortcut extends Mage_Core_Block_Template
{
    public function getCheckoutUrl()
    {
        return $this->getUrl('amazonpayments/asp/checkout');
    }

    public function getButtonImageUrl()
    {
    	return Mage::getStoreConfig('payment/amazonpayments_asp/pay_now_button_image_url');
    }

    public function _toHtml()
    {
        if (Mage::getStoreConfigFlag('payment/amazonpayments_asp/active')
            && Mage::getSingleton('checkout/session')->getQuote()->validateMinimumAmount()) {
            return parent::_toHtml();
        }

        return '';
    }
}