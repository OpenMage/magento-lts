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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml block for fieldset of grouped product
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * @param Mage_Catalog_Model_Product $product
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
     * @param float $price
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
