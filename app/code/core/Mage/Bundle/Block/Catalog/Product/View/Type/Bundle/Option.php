<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle option renderer
 *
 * @category   Mage
 * @package    Mage_Bundle
 *
 * @method Mage_Catalog_Model_Product getFormatProduct()
 * @method $this setFormatProduct(Mage_Catalog_Model_Product $value)
 * @method Mage_Bundle_Model_Option getOption()
 */
class Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option extends Mage_Bundle_Block_Catalog_Product_Price
{
    /**
     * Store preconfigured options
     *
     * @var int|array|string|null
     */
    protected $_selectedOptions = null;

    /**
     * Show if option has a single selection
     *
     * @var bool|null
     */
    protected $_showSingle = null;

    /**
     * Check if option has a single selection
     *
     * @return bool
     */
    protected function _showSingle()
    {
        if (is_null($this->_showSingle)) {
            $option     = $this->getOption();
            $selections = $option->getSelections();

            $this->_showSingle = (count($selections) === 1 && $option->getRequired());
        }

        return $this->_showSingle;
    }

    /**
     * Retrieve default values for template
     *
     * @return array
     */
    protected function _getDefaultValues()
    {
        $option             = $this->getOption();
        $default            = $option->getDefaultSelection();
        $selections         = $option->getSelections();
        $selectedOptions    = $this->_getSelectedOptions();
        $inPreConfigured    = $this->getProduct()->hasPreconfiguredValues()
            && $this->getProduct()->getPreconfiguredValues()
                    ->getData('bundle_option_qty/' . $option->getId());

        if (empty($selectedOptions) && $default) {
            $_defaultQty = $default->getSelectionQty() * 1;
            $_canChangeQty = $default->getSelectionCanChangeQty();
        } elseif (!$inPreConfigured && $selectedOptions && is_numeric($selectedOptions)) {
            $selectedSelection = $option->getSelectionById($selectedOptions);
            if ($selectedSelection) {
                $_defaultQty = $selectedSelection->getSelectionQty() * 1;
                $_canChangeQty = $selectedSelection->getSelectionCanChangeQty();
            } else {
                $_defaultQty = $selections[0]->getSelectionQty() * 1;
                $_canChangeQty = $selections[0]->getSelectionCanChangeQty();
            }
        } elseif (!$this->_showSingle() || $inPreConfigured) {
            $_defaultQty = $this->_getSelectedQty();
            $_canChangeQty = (bool)$_defaultQty;
        } else {
            $_defaultQty = $selections[0]->getSelectionQty() * 1;
            $_canChangeQty = $selections[0]->getSelectionCanChangeQty();
        }

        return [$_defaultQty, $_canChangeQty];
    }

    /**
     * Collect selected options
     *
     * @return int|array|string
     */
    protected function _getSelectedOptions()
    {
        if (is_null($this->_selectedOptions)) {
            $this->_selectedOptions = [];
            $option = $this->getOption();

            if ($this->getProduct()->hasPreconfiguredValues()) {
                $configValue = $this->getProduct()->getPreconfiguredValues()
                    ->getData('bundle_option/' . $option->getId());
                if ($configValue) {
                    $this->_selectedOptions = $configValue;
                } elseif (!$option->getRequired()) {
                    $this->_selectedOptions = 'None';
                }
            }
        }

        return $this->_selectedOptions;
    }

    /**
     * Define if selection is selected
     *
     * @param  Mage_Catalog_Model_Product $selection
     * @return bool
     */
    protected function _isSelected($selection)
    {
        $selectedOptions = $this->_getSelectedOptions();
        if (is_numeric($selectedOptions)) {
            return ($selection->getSelectionId() == $this->_getSelectedOptions());
        } elseif (is_array($selectedOptions) && !empty($selectedOptions)) {
            return in_array($selection->getSelectionId(), $this->_getSelectedOptions());
        } elseif ($selectedOptions === 'None') {
            return false;
        } else {
            return ($selection->getIsDefault() && $selection->isSaleable());
        }
    }

    /**
     * Retrieve selected option qty
     *
     * @return int
     */
    protected function _getSelectedQty()
    {
        if ($this->getProduct()->hasPreconfiguredValues()) {
            $selectedQty = (float)$this->getProduct()->getPreconfiguredValues()
                ->getData('bundle_option_qty/' . $this->getOption()->getId());
            if ($selectedQty < 0) {
                $selectedQty = 0;
            }
        } else {
            $selectedQty = 0;
        }

        return $selectedQty;
    }

    /**
     * Get product model
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', Mage::registry('current_product'));
        }
        return $this->getData('product');
    }

    /**
     * Returns the formatted string for the quantity chosen for the given selection
     *
     * @param Mage_Catalog_Model_Product $selection
     * @param bool $includeContainer
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getSelectionQtyTitlePrice($selection, $includeContainer = true)
    {
        $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $selection);
        $this->setFormatProduct($selection);
        $priceTitle = $selection->getSelectionQty() * 1 . ' x ' . $this->escapeHtml($selection->getName());

        $priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">' : '')
            . '+' . $this->formatPriceString($price, $includeContainer)
            . ($includeContainer ? '</span>' : '');

        return $priceTitle;
    }

    /**
     * Get price for selection product
     *
     * @param Mage_Catalog_Model_Product $selection
     * @return int|float
     */
    public function getSelectionPrice($selection)
    {
        $price = 0;
        $store = $this->getProduct()->getStore();
        if ($selection) {
            $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $selection);
            if (is_numeric($price)) {
                /** @var Mage_Core_Helper_Data $helper */
                $helper = $this->helper('core');
                $price = $helper::currencyByStore($price, $store, false);
            }
        }
        return is_numeric($price) ? $price : 0;
    }

    /**
     * Get title price for selection product
     *
     * @param Mage_Catalog_Model_Product $selection
     * @param bool $includeContainer
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getSelectionTitlePrice($selection, $includeContainer = true)
    {
        $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $selection, 1);
        $this->setFormatProduct($selection);
        $priceTitle = $this->escapeHtml($selection->getName());
        $priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">' : '')
            . '+' . $this->formatPriceString($price, $includeContainer)
            . ($includeContainer ? '</span>' : '');
        return $priceTitle;
    }

    /**
     * Set JS validation container for element
     *
     * @param string $elementId
     * @param string $containerId
     * @return string
     */
    public function setValidationContainer($elementId, $containerId)
    {
        return '<script type="text/javascript">
            $(\'' . $elementId . '\').advaiceContainer = \'' . $containerId . '\';
            $(\'' . $elementId . '\').callbackFunction  = \'bundle.validationCallback\';
            </script>';
    }

    /**
     * Format price string
     *
     * @param float $price
     * @param bool $includeContainer
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function formatPriceString($price, $includeContainer = true)
    {
        $taxHelper  = Mage::helper('tax');
        /** @var Mage_Core_Helper_Data $coreHelper */
        $coreHelper = $this->helper('core');
        $currentProduct = $this->getProduct();
        if ($currentProduct->getPriceType() === Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC
                && $this->getFormatProduct()
        ) {
            $product = $this->getFormatProduct();
        } else {
            $product = $currentProduct;
        }

        $priceTax    = $taxHelper->getPrice($product, $price);
        $priceIncTax = $taxHelper->getPrice($product, $price, true);

        $formated = $coreHelper::currencyByStore($priceTax, $product->getStore(), true, $includeContainer);
        if ($taxHelper->displayBothPrices() && $priceTax != $priceIncTax) {
            $formated .=
                    ' (+' .
                    $coreHelper::currencyByStore($priceIncTax, $product->getStore(), true, $includeContainer) .
                    ' ' . $this->__('Incl. Tax') . ')';
        }

        return $formated;
    }
}
