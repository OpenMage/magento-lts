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
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cart totals grand total renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Xmlconnect_Block_Cart_CartTotals_Grandtotal extends Mage_Tax_Block_Checkout_Grandtotal
{
    /**
     * Total id prefix
     *
     * @var string
     */
    protected $_totalIdPrefix = 'total_';

    /**
     * Add cart grand total to xml
     *
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    protected function _toHtml()
    {
        $grandTotalValue = $this->getTotal()->getValue() ? $this->getTotal()->getValue(): 0;

        /** @var $cartXmlObject Mage_XmlConnect_Model_Simplexml_Element */
        $cartXmlObject = $this->getCartObject();
        $grandTotal = $this->getTotal();
        $code = $grandTotal->getCode();

        /** @var $helper Mage_XmlConnect_Helper_Data */
        $helper = Mage::helper('xmlconnect');

        /** @var $xmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $xmlObj = $cartXmlObject->addCustomChild('total', null, array(
            'id' => $this->getTotalIdPrefix() . $code
        ));

        if ($this->includeTax() && $this->getTotalExclTax() >= 0) {
            $label = $this->__('Grand Total (Excl. Tax)');
            $excludingTaxCode = $code . '_excl_tax';
            $value = $this->getTotalExclTax();
            $formattedValue = $this->getQuote()->getStore()->formatPrice($value);
            $helper->addTotalItemToXmlObj($xmlObj, $excludingTaxCode, $label, $value, $formattedValue);

            $label = $this->__('Grand Total (Incl. Tax)');
            $includingTaxCode = $code . '_incl_tax';
            $value = $grandTotalValue;
            $formattedValue = $this->getQuote()->getStore()->formatPrice($value);
            $helper->addTotalItemToXmlObj($xmlObj, $includingTaxCode, $label, $value, $formattedValue);
        } else {
            $label = $this->__('Grand Total');
            $value = $grandTotalValue;
            $formattedValue = $this->getQuote()->getStore()->formatPrice($value);
            $helper->addTotalItemToXmlObj($xmlObj, $code, $label, $value, $formattedValue);
        }
        return $xmlObj;
    }

    /**
     * Set total id prefix
     *
     * @param string $totalIdPrefix
     * @return Mage_Xmlconnect_Block_Cart_CartTotals_Grandtotal
     */
    public function setTotalIdPrefix($totalIdPrefix)
    {
        $this->_totalIdPrefix = $totalIdPrefix;
        return $this;
    }

    /**
     * Get total id prefix
     *
     * @return string
     */
    public function getTotalIdPrefix()
    {
        return $this->_totalIdPrefix;
    }
}
