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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cart totals gift card renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Xmlconnect_Block_Cart_CartTotals_Nodes_Giftcardaccount extends Mage_Checkout_Block_Total_Default
{
    /**
     * Add gift card balance to xml
     *
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    protected function _toHtml()
    {
        if (!$this->getTotal()->getValue()) {
            return;
        }
        /** @var $cartXmlObject Mage_XmlConnect_Model_Simplexml_Element */
        $cartXmlObject = $this->getCartObject();
        $cards = $this->getTotal()->getGiftCards();
        if (!$cards) {
            $cards = $this->getQuoteGiftCards();
        }
        $code = $this->getTotal()->getCode();

        /** @var $giftCardsXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $giftCardsXmlObj = $cartXmlObject->addCustomChild($code);

        foreach ($cards as $cardCode) {
            $giftCardValue = Mage::helper('xmlconnect')->formatPriceForXml($cardCode['a']);
            $formattedValue = $this->getQuote()->getStore()->formatPrice($giftCardValue, false);
            $giftCardsXmlObj->addCustomChild('item', '-' . $giftCardValue, array(
                'id' => $code . '_' . $cardCode['i'],
                'label' => $this->__('Gift Card (%s)', $cardCode['c']),
                'formatted_value' => '-' . $formattedValue,
                'code' => $cardCode['c']
            ));
        }
        return $giftCardsXmlObj;
    }
}
