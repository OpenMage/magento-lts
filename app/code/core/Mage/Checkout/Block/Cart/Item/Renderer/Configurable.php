<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart item render block
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Block_Cart_Item_Renderer_Configurable extends Mage_Checkout_Block_Cart_Item_Renderer
{
    const CONFIGURABLE_PRODUCT_IMAGE= 'checkout/cart/configurable_product_image';
    const USE_PARENT_IMAGE          = 'parent';

    /**
     * Get item configurable product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getConfigurableProduct()
    {
        if ($option = $this->getItem()->getOptionByCode('product_type')) {
            return $option->getProduct();
        }
        return $this->getProduct();
    }

    /**
     * Get item configurable child product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getChildProduct()
    {
        if ($option = $this->getItem()->getOptionByCode('simple_product')) {
            return $option->getProduct();
        }
        return $this->getProduct();
    }

    /**
     * Get product thumbnail image
     *
     * @return Mage_Catalog_Helper_Image
     */
    public function getProductThumbnail()
    {
        $product = $this->getChildProduct();
        if (!$product || !$product->getData('thumbnail')
            || ($product->getData('thumbnail') === 'no_selection')
            || (Mage::getStoreConfig(self::CONFIGURABLE_PRODUCT_IMAGE) === self::USE_PARENT_IMAGE)) {
            $product = $this->getProduct();
        }

        /** @var Mage_Catalog_Helper_Image $helper */
        $helper = $this->helper('catalog/image');
        return $helper->init($product, 'thumbnail');
    }

    /**
     * Get item product name
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->getProduct()->getName();
    }

    /**
     * Get selected for configurable product attributes
     *
     * @return array
     */
    public function getProductAttributes()
    {
        return $this->getProduct()->getTypeInstance(true)
            ->getSelectedAttributesInfo($this->getProduct());
    }

    /**
     * Get list of all otions for product
     *
     * @return array
     */
    public function getOptionList()
    {
        /** @var Mage_Catalog_Helper_Product_Configuration $helper */
        $helper = Mage::helper('catalog/product_configuration');
        return $helper->getConfigurableOptions($this->getItem());
    }

    /**
     * Retrieve block cache tags
     *
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge(parent::getCacheTags(), $this->getConfigurableProduct()->getCacheIdTags());
    }
}
