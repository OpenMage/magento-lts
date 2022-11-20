<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle option renderer
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * @var int|array|string
     */
    protected $_selectedOptions = null;

    /**
     * Show if option has a single selection
     *
     * @var bool
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
            $_option        = $this->getOption();
            $_selections    = $_option->getSelections();

            $this->_showSingle = (count($_selections) === 1 && $_option->getRequired());
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
        $_option            = $this->getOption();
        $_default           = $_option->getDefaultSelection();
        $_selections        = $_option->getSelections();
        $selectedOptions    = $this->_getSelectedOptions();
        $inPreConfigured    = $this->getProduct()->hasPreconfiguredValues()
            && $this->getProduct()->getPreconfiguredValues()
                    ->getData('bundle_option_qty/' . $_option->getId());

        if (empty($selectedOptions) && $_default) {
            $_defaultQty = $_default->getSelectionQty() * 1;
            $_canChangeQty = $_default->getSelectionCanChangeQty();
        } elseif (!$inPreConfigured && $selectedOptions && is_numeric($selectedOptions)) {
            $selectedSelection = $_option->getSelectionById($selectedOptions);
            if ($selectedSelection) {
                $_defaultQty = $selectedSelection->getSelectionQty() * 1;
                $_canChangeQty = $selectedSelection->getSelectionCanChangeQty();
            } else {
                $_defaultQty = $_selections[0]->getSelectionQty() * 1;
                $_canChangeQty = $_selections[0]->getSelectionCanChangeQty();
            }
        } elseif (!$this->_showSingle() || $inPreConfigured) {
            $_defaultQty = $this->_getSelectedQty();
            $_canChangeQty = (bool)$_defaultQty;
        } else {
            $_defaultQty = $_selections[0]->getSelectionQty() * 1;
            $_canChangeQty = $_selections[0]->getSelectionCanChangeQty();
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
     * @param Mage_Catalog_Model_Product $_selection
     * @param bool $includeContainer
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getSelectionQtyTitlePrice($_selection, $includeContainer = true)
    {
        $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection);
        $this->setFormatProduct($_selection);
        $priceTitle = $_selection->getSelectionQty() * 1 . ' x ' . $this->escapeHtml($_selection->getName());

        $priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">' : '')
            . '+' . $this->formatPriceString($price, $includeContainer)
            . ($includeContainer ? '</span>' : '');

        return $priceTitle;
    }

    /**
     * Get price for selection product
     *
     * @param Mage_Catalog_Model_Product $_selection
     * @return int|float
     */
    public function getSelectionPrice($_selection)
    {
        $price = 0;
        $store = $this->getProduct()->getStore();
        if ($_selection) {
            $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection);
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
     * @param Mage_Catalog_Model_Product $_selection
     * @param bool $includeContainer
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getSelectionTitlePrice($_selection, $includeContainer = true)
    {
        $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection, 1);
        $this->setFormatProduct($_selection);
        $priceTitle = $this->escapeHtml($_selection->getName());
        $priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">' : '')
            . '+' . $this->formatPriceString($price, $includeContainer)
            . ($includeContainer ? '</span>' : '');
        return $priceTitle;
    }

    /**
     * Set JS validation container for element
     *
     * @param int $elementId
     * @param int $containerId
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
