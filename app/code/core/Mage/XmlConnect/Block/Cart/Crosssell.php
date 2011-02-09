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
 * Shopping cart cross sell items xml renderer
 *
 * @category    Mage
 * @package     Mage_Checkout
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
        $crossSellXmlObj = new Mage_XmlConnect_Model_Simplexml_Element('<crosssell></crosssell>');
        if (!$this->getItemCount()) {
            return $crossSellXmlObj->asNiceXml();
        }

        foreach ($this->getItems() as $_item) {
            $itemXmlObj = $crossSellXmlObj->addChild('item');
            $itemXmlObj->addChild('name', $crossSellXmlObj->xmlentities(strip_tags($_item->getName())));
            $icon = $this->helper('catalog/image')->init($_item, 'thumbnail')
                ->resize(Mage::helper('xmlconnect/image')->getImageSizeForContent('product_small'));

            $iconXml = $itemXmlObj->addChild('icon', $icon);

            $file = Mage::helper('xmlconnect')->urlToPath($icon);
            $iconXml->addAttribute('modification_time', filemtime($file));

            $itemXmlObj->addChild('entity_id', $_item->getId());
            $itemXmlObj->addChild('entity_type', $_item->getTypeId());
            $itemXmlObj->addChild('has_options', (int)$_item->getHasOptions());

            if ($this->getChild('product_price')) {
                $this->getChild('product_price')->setProduct($_item)
                   ->setProductXmlObj($itemXmlObj)
                   ->collectProductPrices();
            }

            if (!$_item->getRatingSummary()) {
                Mage::getModel('review/review')
                   ->getEntitySummary($_item, Mage::app()->getStore()->getId());
            }

            $itemXmlObj->addChild('rating_summary', round((int)$_item->getRatingSummary()->getRatingSummary() / 10));
            $itemXmlObj->addChild('reviews_count', $_item->getRatingSummary()->getReviewsCount());
        }

        return $crossSellXmlObj->asNiceXml();
    }

}
