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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer wishlist xml renderer
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_Block_Wishlist extends Mage_Wishlist_Block_Customer_Wishlist
{
    /**
     * Render customer wishlist xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $wishlistXmlObj = new Mage_XmlConnect_Model_Simplexml_Element('<wishlist></wishlist>');
        $hasMoreItems = 0;
        /**
         * Apply offset and count
         */
        $request= $this->getRequest();
        $offset = (int)$request->getParam('offset', 0);
        $count  = (int)$request->getParam('count', 0);
        $count  = $count <= 0 ? 1 : $count;
        if ($offset + $count < $this->getWishlist()->getSize()) {
            $hasMoreItems = 1;
        }
        $this->getWishlist()->getSelect()->limit($count, $offset);

        $wishlistXmlObj->addAttribute('items_count', $this->getWishlistItemsCount());
        $wishlistXmlObj->addAttribute('has_more_items', $hasMoreItems);

        if ($this->hasWishlistItems()) {
            /**
             * @var Mage_Wishlist_Model_Mysql4_Product_Collection
             */
            foreach ($this->getWishlist() as $item) {
                $itemXmlObj = $wishlistXmlObj->addChild('item');
                $itemXmlObj->addChild('item_id', $item->getWishlistItemId());
                $itemXmlObj->addChild('entity_id', $item->getProductId());
                $itemXmlObj->addChild('entity_type_id', $item->getTypeId());
                $itemXmlObj->addChild('name', $wishlistXmlObj->xmlentities(strip_tags($item->getName())));
                $itemXmlObj->addChild('in_stock', (int)$item->isSalable());
                /**
                 * If product type is grouped than it has options as its grouped items
                 */
                if ($item->getTypeId() == Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE) {
                    $item->setHasOptions(true);
                }
                $itemXmlObj->addChild('has_options', (int)$item->getHasOptions());

                $icon = $this->helper('catalog/image')->init($item, 'small_image')
                    ->resize(Mage::helper('xmlconnect/image')->getImageSizeForContent('product_small'));

                $iconXml = $itemXmlObj->addChild('icon', $icon);

                $file = Mage::helper('xmlconnect')->urlToPath($icon);
                $iconXml->addAttribute('modification_time', filemtime($file));


                $itemXmlObj->addChild('description', $wishlistXmlObj->xmlentities(strip_tags($item->getWishlistItemDescription())));
                $itemXmlObj->addChild('added_date', $wishlistXmlObj->xmlentities($this->getFormatedDate($item->getAddedAt())));

                if ($this->getChild('product_price')) {
                    $this->getChild('product_price')->setProduct($item)
                       ->setProductXmlObj($itemXmlObj)
                       ->collectProductPrices();
                }

                if (!$item->getRatingSummary()) {
                    Mage::getModel('review/review')
                       ->getEntitySummary($item, Mage::app()->getStore()->getId());
                }

                $itemXmlObj->addChild('rating_summary', round((int)$item->getRatingSummary()->getRatingSummary() / 10));
                $itemXmlObj->addChild('reviews_count', $item->getRatingSummary()->getReviewsCount());
            }
        }

        return $wishlistXmlObj->asNiceXml();
    }
}
