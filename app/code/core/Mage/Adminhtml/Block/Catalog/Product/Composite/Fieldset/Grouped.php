<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml block for fieldset of grouped product
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Grouped extends Mage_Catalog_Block_Product_View_Type_Grouped
{
    /**
     * Redefine default price block
     * Set current customer to tax calculation
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_block = 'adminhtml/catalog_product_price';
        $this->_useLinkForAsLowAs = false;

        $taxCalculation = Mage::getSingleton('tax/calculation');
        if (!$taxCalculation->getCustomer() && Mage::registry('current_customer')) {
            $taxCalculation->setCustomer(Mage::registry('current_customer'));
        }
    }

    /**
     * Retrieve product
     *
     * @return Mage_Catalog_Model_Product
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', Mage::registry('product'));
        }

        $product = $this->getData('product');
        if (is_null($product->getTypeInstance(true)->getStoreFilter($product))) {
            $product->getTypeInstance(true)->setStoreFilter(Mage::app()->getStore($product->getStoreId()), $product);
        }

        return $product;
    }

    /**
     * Retrieve array of associated products
     *
     * @return array
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getAssociatedProducts()
    {
        $product = $this->getProduct();
        /** @var Mage_Catalog_Model_Product_Type_Grouped $productType */
        $productType = $product->getTypeInstance(true);
        $result = $productType->getAssociatedProducts($product);

        $storeId = $product->getStoreId();
        foreach ($result as $item) {
            $item->setStoreId($storeId);
        }

        return $result;
    }

    /**
     * Set preconfigured values to grouped associated products
     *
     * @return Mage_Catalog_Block_Product_View_Type_Grouped
     * @throws Mage_Core_Model_Store_Exception
     */
    public function setPreconfiguredValue()
    {
        $configValues = $this->getProduct()->getPreconfiguredValues()->getSuperGroup();
        if (is_array($configValues)) {
            $associatedProducts = $this->getAssociatedProducts();
            foreach ($associatedProducts as $item) {
                if (isset($configValues[$item->getId()])) {
                    $item->setQty($configValues[$item->getId()]);
                }
            }
        }

        return $this;
    }

    /**
     * Check whether the price can be shown for the specified product
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function getCanShowProductPrice($product)
    {
        return true;
    }

    /**
     * Checks whether block is last fieldset in popup
     *
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getIsLastFieldset()
    {
        $isLast = $this->getData('is_last_fieldset');
        if (!$isLast) {
            $options = $this->getProduct()->getOptions();
            return !$options || !count($options);
        }

        return $isLast;
    }

    /**
     * Returns price converted to current currency rate
     *
     * @param  float                           $price
     * @return float
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCurrencyPrice($price)
    {
        /** @var Mage_Core_Helper_Data $helper */
        $helper = $this->helper('core');
        $store = $this->getProduct()->getStore();
        return $helper::currencyByStore($price, $store, false);
    }
}
