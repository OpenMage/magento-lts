<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Checkout
 */

/**
 * Shopping cart item render block
 *
 * @package    Mage_Checkout
 *
 * @method \Mage_Checkout_Block_Cart_Sidebar getRenderedBlock()
 */
class Mage_Checkout_Block_Cart_Item_Renderer_Grouped extends Mage_Checkout_Block_Cart_Item_Renderer
{
    public const GROUPED_PRODUCT_IMAGE = 'checkout/cart/grouped_product_image';

    public const USE_PARENT_IMAGE      = 'parent';

    /**
     * Get item grouped product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getGroupedProduct()
    {
        $option = $this->getItem()->getOptionByCode('product_type');
        if ($option) {
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
        $product = $this->getProduct();
        if (!$product->getData('thumbnail')
            || ($product->getData('thumbnail') === 'no_selection')
            || (Mage::getStoreConfig(self::GROUPED_PRODUCT_IMAGE) === self::USE_PARENT_IMAGE)
        ) {
            $product = $this->getGroupedProduct();
        }

        /** @var Mage_Catalog_Helper_Image $helper */
        $helper = $this->helper('catalog/image');
        return $helper->init($product, 'thumbnail');
    }

    /**
     * Prepare item html
     *
     * This method uses renderer for real product type
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var Mage_Checkout_Block_Cart_Item_Renderer $renderer */
        $renderer = $this->getRenderedBlock()->getItemRenderer($this->getItem()->getRealProductType());
        $renderer->setItem($this->getItem());
        $renderer->overrideProductThumbnail($this->getProductThumbnail());

        $rendererHtml = $renderer->toHtml();
        $renderer->overrideProductThumbnail(null);
        return $rendererHtml;
    }

    /**
     * Retrieve block cache tags
     *
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge(parent::getCacheTags(), $this->getGroupedProduct()->getCacheIdTags());
    }
}
