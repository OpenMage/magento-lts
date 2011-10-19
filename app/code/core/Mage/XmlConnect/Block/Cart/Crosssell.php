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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

        $crossSellXmlObj = Mage::getModel('xmlconnect/simplexml_element', '<crosssell></crosssell>');
        if (!$this->getItemCount()) {
            return $crossSellXmlObj->asNiceXml();
        }

        /** @var $product Mage_Catalog_Model_Product */
        foreach ($this->getItems() as $product) {
            $itemXmlObj = $crossSellXmlObj->addChild('item');
            $itemXmlObj->addChild('name', $crossSellXmlObj->xmlentities($product->getName()));
            $icon = $this->helper('catalog/image')->init($product, 'thumbnail')
                ->resize(Mage::helper('xmlconnect/image')->getImageSizeForContent('product_small'));

            $iconXml = $itemXmlObj->addChild('icon', $icon);

            $file = Mage::helper('xmlconnect')->urlToPath($icon);
            $iconXml->addAttribute('modification_time', filemtime($file));

            $itemXmlObj->addChild('entity_id', $product->getId());
            $itemXmlObj->addChild('entity_type', $product->getTypeId());

            /**
             * If product type is grouped than it has options as its grouped items
             */
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE) {
                $product->setHasOptions(true);
            }

            $itemXmlObj->addChild('has_options', (int)$product->getHasOptions());
            $itemXmlObj->addChild('in_stock', (int)$product->getIsInStock());
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
