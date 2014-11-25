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
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cart totals default renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Xmlconnect_Block_Cart_CartTotals_Default extends Mage_Checkout_Block_Total_Default
{
    /**
     * Total id prefix
     *
     * @var string
     */
    protected $_totalIdPrefix = 'total_';

    /**
     * Add cart total to xml
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
        $currentTotal = $this->getTotal();

        /** @var $xmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $xmlObj = $cartXmlObject->addCustomChild('total', null, array(
            'id' => $this->getTotalIdPrefix() . $currentTotal->getCode()
        ));
        $value = Mage::helper('xmlconnect')->formatPriceForXml($currentTotal->getValue());
        $formattedValue = $this->getQuote()->getStore()->formatPrice($value, false);

        $xmlObj->addCustomChild('item', $value, array(
            'id' => $currentTotal->getCode(),
            'label' => $currentTotal->getTitle(),
            'formatted_value' => $formattedValue
        ));
        return $xmlObj;
    }

    /**
     * Set total id prefix
     *
     * @param string $totalIdPrefix
     * @return Mage_Xmlconnect_Block_Cart_CartTotals_Default
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
