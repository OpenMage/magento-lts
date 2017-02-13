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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class Mage_ConfigurableSwatches_Block_Catalog_Media_Js_Abstract extends Mage_Core_Block_Template
{
    protected $_template = 'configurableswatches/catalog/media/js.phtml';

    /**
     * A list of blocks that contain products. Used to get the current display mode (grid/list).
     *
     * @var array
     */
    protected $_productListBlocks = array('product_list', 'search_result_list');

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
    protected function _getJsImageFallbackString(array $imageFallback) {
        /* @var $coreHelper Mage_Core_Helper_Data */
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
    public function getProductImageFallbacks($keepFrame = null) {
        /* @var $helper Mage_ConfigurableSwatches_Helper_Mediafallback */
        $helper = Mage::helper('configurableswatches/mediafallback');

        $fallbacks = array();

        $products = $this->getProducts();

        if ($keepFrame === null) {
            $keepFrame = $this->isKeepFrame();
        }

        /* @var $product Mage_Catalog_Model_Product */
        foreach ($products as $product) {
            $imageFallback = $helper->getConfigurableImagesFallbackArray($product, $this->_getImageSizes(), $keepFrame);

            $fallbacks[$product->getId()] = array(
                'product' => $product,
                'image_fallback' => $this->_getJsImageFallbackString($imageFallback)
            );
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
    public function getImageType() {
        return parent::getImageType();
    }

    /**
     * Prevent actual block render if we are disabled, and i.e. via the module
     * config as opposed to the advanced module settings page
     *
     * @return string
     */
    protected function _toHtml() {
        if (!Mage::helper('configurableswatches')->isEnabled()) { // functionality disabled
            return ''; // do not render block
        }
        return parent::_toHtml();
    }
}
