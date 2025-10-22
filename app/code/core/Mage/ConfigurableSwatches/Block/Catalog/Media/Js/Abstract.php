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
abstract class Mage_ConfigurableSwatches_Block_Catalog_Media_Js_Abstract extends Mage_Core_Block_Template
{
    protected $_template = 'configurableswatches/catalog/media/js.phtml';

    /**
     * A list of blocks that contain products. Used to get the current display mode (grid/list).
     *
     * @var array
     */
    protected $_productListBlocks = ['product_list', 'search_result_list'];

    /**
     * Get target product IDs
     *
     * @return array
     */
    abstract public function getProducts();

    /**
     * json encode image fallback array
     *
     * @return string
     */
    protected function _getJsImageFallbackString(array $imageFallback)
    {
        /** @var Mage_Core_Helper_Data $coreHelper */
        $coreHelper = Mage::helper('core');

        return $coreHelper->jsonEncode($imageFallback);
    }

    /**
     * Image size(s) to attach to children products as array
     *
     * @return array
     */
    abstract protected function _getImageSizes();

    /**
     * Get image fallbacks by product as
     * array(product ID => array( product => product, image_fallback => image fallback ) )
     *
     * @param bool|null $keepFrame
     * @return array
     */
    public function getProductImageFallbacks($keepFrame = null)
    {
        /** @var Mage_ConfigurableSwatches_Helper_Mediafallback $helper */
        $helper = Mage::helper('configurableswatches/mediafallback');

        $fallbacks = [];

        $products = $this->getProducts();

        if ($keepFrame === null) {
            $keepFrame = $this->isKeepFrame();
        }

        /** @var Mage_Catalog_Model_Product $product */
        foreach ($products as $product) {
            $imageFallback = $helper->getConfigurableImagesFallbackArray($product, $this->_getImageSizes(), $keepFrame);

            $fallbacks[$product->getId()] = [
                'product' => $product,
                'image_fallback' => $this->_getJsImageFallbackString($imageFallback),
            ];
        }

        return $fallbacks;
    }

    /**
     * Is need keep frame
     *
     * @return bool
     */
    public function isKeepFrame()
    {
        $keepFrame = false;
        foreach ($this->_productListBlocks as $blockName) {
            $listBlock = $this->getLayout()->getBlock($blockName);

            if ($listBlock && $listBlock->getMode() == 'grid') {
                $keepFrame = true;
                break;
            }
        }

        return $keepFrame;
    }

    /**
     * Get image type to pass to configurable media image JS
     *
     * @return string
     */
    public function getImageType()
    {
        return parent::getImageType();
    }

    /**
     * Prevent actual block render if we are disabled, and i.e. via the module
     * config as opposed to the advanced module settings page
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!Mage::helper('configurableswatches')->isEnabled()) { // functionality disabled
            return ''; // do not render block
        }

        return parent::_toHtml();
    }
}
