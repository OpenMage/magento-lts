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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Home categories list renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Home extends Mage_XmlConnect_Block_Catalog
{
    /**
     * Category list limitation
     */
    const HOME_PAGE_CATEGORIES_COUNT = 6;

    /**
     * Render home category list xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $homeXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $homeXmlObj = Mage::getModel('xmlconnect/simplexml_element', '<home></home>');

        $categoryCollection = array();
        $helper = Mage::helper('catalog/category');
        $categoryCount = 0;
        foreach ($helper->getStoreCategories() as $child) {
            if ($child->getIsActive()) {
                $categoryCollection[] = $child;
                $categoryCount++;
            }
            if ($categoryCount == self::HOME_PAGE_CATEGORIES_COUNT) {
                break;
            }
        }

        if (sizeof($categoryCollection)) {
            $itemsXmlObj = $homeXmlObj->addChild('categories');
        }

        foreach ($categoryCollection as $item) {
            /** @var $item Mage_Catalog_Model_Category */
            $item = Mage::getModel('catalog/category')->load($item->getId());
            if (!$item->getIncludeInMenu()) {
                continue;
            }
            $itemXmlObj = $itemsXmlObj->addChild('item');
            $itemXmlObj->addChild('label', $homeXmlObj->escapeXml($item->getName()));
            $itemXmlObj->addChild('entity_id', $item->getId());
            $itemXmlObj->addChild('content_type', $item->hasChildren() ? 'categories' : 'products');
            $icon = Mage::helper('xmlconnect/catalog_category_image')->initialize($item, 'thumbnail')
                ->resize(Mage::getModel('xmlconnect/images')->getImageLimitParam('content/category'));

            $iconXml = $itemXmlObj->addChild('icon', $icon);
            $iconXml->addAttribute('modification_time', filemtime($icon->getNewFile()));
        }
        $homeXmlObj->addChild('home_banner', '/current/media/catalog/category/banner_home.png');

        return $homeXmlObj->asNiceXml();
    }
}
