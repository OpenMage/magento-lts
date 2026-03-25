<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rss
 */

/**
 * @package    Mage_Rss
 */
class Mage_Rss_Block_Catalog_Abstract extends Mage_Rss_Block_Abstract
{
    /**
     * Stored price block instances
     * @var Mage_Core_Block_Abstract[]
     */
    protected $_priceBlock = [];

    /**
     * Stored price blocks info
     * @var array<string, array{block: string, template: string}>
     */
    protected $_priceBlockTypes = [];

    /**
     * Default values for price block and template
     * @var string
     */
    protected $_priceBlockDefaultTemplate = 'catalog/rss/product/price.phtml';

    /**
     * Default price block type
     * @var string
     */
    protected $_priceBlockDefaultType = 'catalog/product_price';

    /**
     * Whether to show "As low as" as a link
     * @var bool
     */
    protected $_useLinkForAsLowAs = true;

    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_rss';

    /**
     * Return Price Block renderer for specified product type
     *
     * @param  string                   $productTypeId Catalog Product type
     * @return Mage_Core_Block_Abstract
     */
    protected function _getPriceBlock($productTypeId)
    {
        if (!isset($this->_priceBlock[$productTypeId])) {
            $block = $this->_priceBlockDefaultType;
            if (isset($this->_priceBlockTypes[$productTypeId])) {
                if ($this->_priceBlockTypes[$productTypeId]['block'] != '') {
                    $block = $this->_priceBlockTypes[$productTypeId]['block'];
                }
            }

            $this->_priceBlock[$productTypeId] = $this->getLayout()->createBlock($block);
        }

        return $this->_priceBlock[$productTypeId];
    }

    /**
     * Return template for Price Block renderer
     *
     * @param  string $productTypeId Catalog Product type
     * @return string
     */
    protected function _getPriceBlockTemplate($productTypeId)
    {
        if (isset($this->_priceBlockTypes[$productTypeId])) {
            if ($this->_priceBlockTypes[$productTypeId]['template'] != '') {
                return $this->_priceBlockTypes[$productTypeId]['template'];
            }
        }

        return $this->_priceBlockDefaultTemplate;
    }

    /**
     * Returns product price html for RSS feed
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  bool                       $displayMinimalPrice display "As low as" etc
     * @param  string                     $idSuffix            Suffix for HTML containers
     * @return string
     */
    public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix = '')
    {
        $typeId = $product->getTypeId();
        if (Mage::helper('catalog')->canApplyMsrp($product)) {
            $typeId = $this->_mapRenderer;
        }

        return $this->_getPriceBlock($typeId)
            ->setTemplate($this->_getPriceBlockTemplate($typeId))
            ->setProduct($product)
            ->setDisplayMinimalPrice($displayMinimalPrice)
            ->setIdSuffix($idSuffix)
            ->setUseLinkForAsLowAs($this->_useLinkForAsLowAs)
            ->toHtml();
    }

    /**
     * Adding customized price template for product type, used as action in layouts
     *
     * @param  string $type     Catalog Product Type
     * @param  string $block    Block Type
     * @param  string $template Template
     * @return void
     */
    public function addPriceBlockType($type, $block = '', $template = '')
    {
        if ($type) {
            $this->_priceBlockTypes[$type] = [
                'block' => $block,
                'template' => $template,
            ];
        }
    }
}
