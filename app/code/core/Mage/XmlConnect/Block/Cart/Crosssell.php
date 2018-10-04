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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart cross sell items xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Cart_Crosssell extends Mage_Checkout_Block_Cart_Crosssell
{
    /**
     * Render cross sell items xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (is_object(Mage::getConfig()->getNode('modules/Enterprise_TargetRule'))) {
            $blockRenderer = 'enterprise_targetrule/checkout_cart_crosssell';
            $blockName = 'targetrule.checkout.cart.crosssell';
            $this->getLayout()->createBlock($blockRenderer, $blockName);
            $this->setItems($this->getLayout()->getBlock($blockName)->getItemCollection());
        }
        /** @var $crossSellXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $crossSellXmlObj = Mage::getModel('xmlconnect/simplexml_element', '<crosssell></crosssell>');
        if (!$this->getItemCount()) {
            return $crossSellXmlObj->asNiceXml();
        }

        $productSmallImageSize = Mage::getModel('xmlconnect/images')->getImageLimitParam('content/product_small');
        /** @var $productImageHelper Mage_XmlConnect_Helper_Catalog_Product_Image */
        $productImageHelper = $this->helper('xmlconnect/catalog_product_image');
        /** @var $product Mage_Catalog_Model_Product */
        foreach ($this->getItems() as $product) {
            $itemXmlObj = $crossSellXmlObj->addChild('item');
            $itemXmlObj->addChild('name', $crossSellXmlObj->escapeXml($product->getName()));
            $icon = $productImageHelper->init($product, 'thumbnail')->resize($productSmallImageSize);

            $iconXml = $itemXmlObj->addChild('icon', $icon);
            $iconXml->addAttribute('modification_time', filemtime($icon->getNewFile()));

            $itemXmlObj->addChild('entity_id', $product->getId());
            $itemXmlObj->addChild('entity_type', $product->getTypeId());

            /**
             * If product type is grouped than it has options as its grouped items
             */
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE
                || $product->getTypeId() == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
                $product->setHasOptions(true);
            }

            $itemXmlObj->addChild('has_options', (int)$product->getHasOptions());
            $itemXmlObj->addChild('in_stock', (int)$product->getStockItem()->getIsInStock());
            if ($product->getTypeId() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
                $itemXmlObj->addChild('is_salable', 0);
            } else {
                $itemXmlObj->addChild('is_salable', (int)$product->isSalable());
            }

            if ($this->getChild('product_price')) {
                $this->getChild('product_price')->setProduct($product)->setProductXmlObj($itemXmlObj)
                    ->collectProductPrices();
            }

            if (!$product->getRatingSummary()) {
                Mage::getModel('review/review')->getEntitySummary($product, Mage::app()->getStore()->getId());
            }

            $itemXmlObj->addChild('rating_summary', round((int)$product->getRatingSummary()->getRatingSummary() / 10));
            $itemXmlObj->addChild('reviews_count', $product->getRatingSummary()->getReviewsCount());
        }
        return $crossSellXmlObj->asNiceXml();
    }
}
