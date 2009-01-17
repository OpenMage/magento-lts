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
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Bundle option renderer
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option extends Mage_Bundle_Block_Catalog_Product_Price
{
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', Mage::registry('current_product'));
        }
        return $this->getData('product');
    }

    public function getSelectionQtyTitlePrice($_selection, $includeContainer = true)
    {
        $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection);
        return $_selection->getSelectionQty()*1 . ' x ' . $_selection->getName() . ' &nbsp; ' .
            ($includeContainer ? '<span class="price-notice">':'') . '+' .
            $this->formatPriceString($price, $includeContainer) . ($includeContainer ? '</span>':'');
    }

    public function getSelectionTitlePrice($_selection, $includeContainer = true)
    {
        $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection, 1);
        return $_selection->getName() . ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">':'') . '+' .
            $this->formatPriceString($price, $includeContainer) . ($includeContainer ? '</span>':'');
    }

    public function setValidationContainer($elementId, $containerId)
    {
        return '<script type="text/javascript">
            $(\'' . $elementId . '\').advaiceContainer = \'' . $containerId . '\';
            $(\'' . $elementId . '\').callbackFunction  = \'bundle.validationCallback\';
            </script>';
    }

    public function formatPriceString($price, $includeContainer = true)
    {
        $priceTax = Mage::helper('tax')->getPrice($this->getProduct(), $price);
        $priceIncTax = Mage::helper('tax')->getPrice($this->getProduct(), $price, true);

        if (Mage::helper('tax')->displayBothPrices() && $priceTax != $priceIncTax) {
            $formated = Mage::helper('core')->currency($priceTax, true, $includeContainer);
            $formated .= ' (+'.Mage::helper('core')->currency($priceIncTax, true, $includeContainer).' '.Mage::helper('tax')->__('Incl. Tax').')';
        } else {
            $formated = $this->helper('core')->currency($priceTax, true, $includeContainer);
        }

        return $formated;
    }
}
