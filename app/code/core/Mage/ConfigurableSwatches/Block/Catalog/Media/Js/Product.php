<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ConfigurableSwatches
 */

/**
 * @package    Mage_ConfigurableSwatches
 */
class Mage_ConfigurableSwatches_Block_Catalog_Media_Js_Product extends Mage_ConfigurableSwatches_Block_Catalog_Media_Js_Abstract
{
    /**
     * Return array of single product -- current product
     *
     * @return Mage_Catalog_Model_Product[]
     */
    public function getProducts()
    {
        $product = Mage::registry('product');

        if (!$product) {
            return [];
        }

        return [$product];
    }

    /**
     * Default to base image type
     *
     * @return string
     */
    public function getImageType()
    {
        $type = parent::getImageType();

        if (empty($type)) {
            return Mage_ConfigurableSwatches_Helper_Productimg::MEDIA_IMAGE_TYPE_BASE;
        }

        return $type;
    }

    /**
     * instruct image image type to be loaded
     *
     * @return string[]
     */
    protected function _getImageSizes()
    {
        return ['image'];
    }

    /**
     * Override parent to check if swatches are enabled for product detail pages
     * Product detail swatches work independently of listing attribute configuration
     *
     * @return string
     */
    protected function _toHtml()
    {
        // For product detail pages, only check if swatches are enabled in config
        // Don't require listing attribute to be configured
        if (!Mage::getStoreConfigFlag(Mage_ConfigurableSwatches_Helper_Data::CONFIG_PATH_ENABLED)) {
            return ''; // do not render block
        }

        // Call grandparent to skip the Abstract class's isEnabled() check
        return Mage_Core_Block_Template::_toHtml();
    }
}
