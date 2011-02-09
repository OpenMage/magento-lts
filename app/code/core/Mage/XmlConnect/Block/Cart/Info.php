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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart summary information xml renderer
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Cart_Info extends Mage_XmlConnect_Block_Cart
{
   /**
     * Render cart summary xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $quote = $this->getQuote();
        $xmlObject  = new Mage_XmlConnect_Model_Simplexml_Element('<cart></cart>');
        $xmlObject->addChild('is_virtual', (int)$this->helper('checkout/cart')->getIsVirtualQuote());
        $xmlObject->addChild('summary_qty', (int)$this->helper('checkout/cart')->getSummaryCount());
        $xmlObject->addChild('virtual_qty', (int)$quote->getItemVirtualQty());
        if (strlen($quote->getCouponCode())) {
            $xmlObject->addChild('has_coupon_code', 1);
        }

        $totalsXml = $this->getChildHtml('totals');
        if ($totalsXml) {
            $totalsXmlObj = new Mage_XmlConnect_Model_Simplexml_Element($totalsXml);
            $xmlObject->appendChild($totalsXmlObj);
        }

        return $xmlObject->asNiceXml();
    }

}
