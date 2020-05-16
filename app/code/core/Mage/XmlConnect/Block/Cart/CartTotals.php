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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart totals xml renderer
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Cart_CartTotals extends Mage_Checkout_Block_Cart_Totals
{
    /**
     * Default totals renderer
     *
     * @var string
     */
    protected $_defaultRenderer = 'xmlconnect/cart_cartTotals_default';

    /**
     * Render cart totals xml
     *
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    protected function _toHtml()
    {
        /** @var $cartXmlObject Mage_XmlConnect_Model_Simplexml_Element */
        $cartXmlObject = $this->getCartXmlObject();
        /** @var $totalsXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $totalsXmlObj = Mage::getModel('xmlconnect/simplexml_element', '<totals></totals>');

        foreach($this->getTotals() as $total) {
            $code = $total->getCode();
            if ($total->getAs()) {
                $code = $total->getAs();
            }
            $isNode = Mage::getConfig()->getNode("global/xmlconnect/sales/quote/totals/{$code}/is_node");
            if ($isNode) {
                $this->_getTotalRenderer($code)->setTotal($total)->setCartObject($cartXmlObject)->toHtml();
            } else {
                $this->_getTotalRenderer($code)->setTotal($total)->setCartObject($totalsXmlObj)->toHtml();
            }
        }
        $cartXmlObject->appendChild($totalsXmlObj);
        return $this;
    }

    /**
     * Get renderer block
     *
     * @param string $code
     * @return Mage_Core_Block_Abstract
     */
    protected function _getTotalRenderer($code)
    {
        $blockName = $code . '_total_renderer';
        $block = $this->getLayout()->getBlock($blockName);
        if (!$block) {
            $block = $this->_defaultRenderer;
            $config = Mage::getConfig()->getNode("global/xmlconnect/sales/quote/totals/{$code}/renderer");
            if ($config) {
                $block = (string) $config;
            }
            $block = $this->getLayout()->createBlock($block, $blockName);
        }
        /**
         * Transfer totals to renderer
         */
        $block->setTotals($this->getTotals());
        return $block;
    }
}
