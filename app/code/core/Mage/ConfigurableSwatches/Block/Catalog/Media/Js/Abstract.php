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
 * @package    Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 * @author     Magento Core Team <core@magentocommerce.com>
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
     * @param array $imageFallback
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
     * @param null $keepFrame
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
                'image_fallback' => $this->_getJsImageFallbackString($imageFallback)
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
