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
 * Category list xml renderer
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_Block_Catalog_Category extends Mage_XmlConnect_Block_Catalog
{
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $categoryXmlObj = new Mage_XmlConnect_Model_Simplexml_Element('<category></category>');
        $categoryId     = $this->getRequest()->getParam('id', null);
        if ($categoryId === null) {
            $categoryId = Mage::app()->getStore()->getRootCategoryId();
        }

        $productsXmlObj = $productListBlock = false;

        $categoryModel  = Mage::getModel('catalog/category')->load($categoryId);
        if ($categoryModel->getId()) {
            $hasMoreProductItems = 0;
            $productListBlock = $this->getChild('product_list');
            if ($productListBlock && $categoryModel->getLevel() > 1) {
                $layer = Mage::getSingleton('catalog/layer');
                $productsXmlObj = $productListBlock->setCategory($categoryModel)
                    ->setLayer($layer)
                    ->getProductsXmlObject();
                $hasMoreProductItems = (int)$productListBlock->getHasProductItems();
            }

            $infoBlock = $this->getChild('category_info');
            if ($infoBlock) {
                $categoryInfoXmlObj = $infoBlock->setCategory($categoryModel)
                    ->getCategoryInfoXmlObject();
                $categoryInfoXmlObj->addChild('has_more_items', $hasMoreProductItems);
                $categoryXmlObj->appendChild($categoryInfoXmlObj);
            }
        }

        /** @var $categoryCollection Mage_XmlConnect_Model_Mysql4_Category_Collection */
        $categoryCollection = Mage::getResourceModel('xmlconnect/category_collection');
        $categoryCollection->setStoreId(Mage::app()->getStore()->getId())
            ->setOrder('position', 'ASC')
            ->addParentIdFilter($categoryId);

        // subcategories are exists
        if (sizeof($categoryCollection)) {
            $itemsXmlObj = $categoryXmlObj->addChild('items');

            foreach ($categoryCollection->getItems() as $item) {
                $itemXmlObj = $itemsXmlObj->addChild('item');
                $itemXmlObj->addChild('label', $categoryXmlObj->xmlentities(strip_tags($item->getName())));
                $itemXmlObj->addChild('entity_id', $item->getEntityId());
                $itemXmlObj->addChild('content_type', $item->hasChildren() ? 'categories' : 'products');
                if (!is_null($categoryId)) {
                    $itemXmlObj->addChild('parent_id', $item->getParentId());
                }
                $icon = Mage::helper('xmlconnect/catalog_category_image')->initialize($item, 'thumbnail')
                    ->resize(Mage::helper('xmlconnect/image')->getImageSizeForContent('category'));

                $iconXml = $itemXmlObj->addChild('icon', $icon);

                $file = Mage::helper('xmlconnect')->urlToPath($icon);
                $iconXml->addAttribute('modification_time', filemtime($file));
            }
        }

        if ($productListBlock && $productsXmlObj) {
            $categoryXmlObj->appendChild($productsXmlObj);
        }

        return $categoryXmlObj->asNiceXml();
    }
}
