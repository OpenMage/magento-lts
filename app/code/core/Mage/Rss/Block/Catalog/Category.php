<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rss
 */

/**
 * Review form block
 *
 * @package    Mage_Rss
 */
class Mage_Rss_Block_Catalog_Category extends Mage_Rss_Block_Catalog_Abstract
{
    /**
     * @throws Exception
     */
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey(
            'rss_catalog_category_'
            . $this->getRequest()->getParam('cid') . '_'
            . $this->getRequest()->getParam('store_id') . '_'
            . Mage::getModel('customer/session')->getId(),
        );
        $this->setCacheLifetime(600);
    }

    /**
     * @return string
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     * @throws Exception
     */
    protected function _toHtml()
    {
        $categoryId = $this->getRequest()->getParam('cid');
        $storeId = $this->_getStoreId();
        $rssObj = Mage::getModel('rss/rss');
        if ($categoryId) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            if ($category->getId()) {
                $layer = Mage::getSingleton('catalog/layer')->setStore($storeId);
                //want to load all products no matter anchor or not
                $category->setIsAnchor(1);
                $newurl = $category->getUrl();
                $title = $category->getName();
                $data = ['title' => $title,
                    'description' => $title,
                    'link'        => $newurl,
                    'charset'     => 'UTF-8',
                ];

                $rssObj->_addHeader($data);

                /** @var Mage_Catalog_Model_Resource_Category_Collection $collection */
                $collection = $category->getCollection();
                $collection->addAttributeToSelect('url_key')
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('is_anchor')
                    ->addAttributeToFilter('is_active', 1)
                    ->addIdFilter($category->getChildren())
                    ->load()
                ;
                $productCollection = Mage::getModel('catalog/product')->getCollection();

                $currentCategory = $layer->setCurrentCategory($category);
                $layer->prepareProductCollection($productCollection);
                $productCollection->addCountToCategories($collection);

                $category->getProductCollection()->setStoreId($storeId);
                /*
                 * only load latest 50 products
                 */
                $categoryProductCollection = $currentCategory
                    ->getProductCollection()
                    ->addAttributeToSort('updated_at', 'desc')
                    ->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds())
                    ->setCurPage(1)
                    ->setPageSize(50)
                ;

                if ($categoryProductCollection->getSize() > 0) {
                    $args = ['rssObj' => $rssObj];
                    foreach ($categoryProductCollection as $categoryProduct) {
                        $args['product'] = $categoryProduct;
                        $this->addNewItemXmlCallback($args);
                    }
                }
            }
        }
        return $rssObj->createRssXml();
    }

    /**
     * Preparing data and adding to rss object
     *
     * @param array $args
     */
    public function addNewItemXmlCallback($args)
    {
        $product = $args['product'];
        $product->setAllowedInRss(true);
        $product->setAllowedPriceInRss(true);

        Mage::dispatchEvent('rss_catalog_category_xml_callback', $args);

        if (!$product->getAllowedInRss()) {
            return;
        }

        /** @var Mage_Catalog_Helper_Image $helper */
        $helper = $this->helper('catalog/image');

        $description = '<table><tr>'
                     . '<td><a href="' . $product->getProductUrl() . '"><img src="'
                     . $helper->init($product, 'thumbnail')->resize(75, 75)
                     . '" border="0" align="left" height="75" width="75"></a></td>'
                     . '<td  style="text-decoration:none;">' . $product->getDescription();

        if ($product->getAllowedPriceInRss()) {
            $description .= $this->getPriceHtml($product, true);
        }

        $description .= '</td></tr></table>';
        $rssObj = $args['rssObj'];
        $data = [
            'title'         => $product->getName(),
            'link'          => $product->getProductUrl(),
            'description'   => $description,
        ];

        $rssObj->_addEntry($data);
    }
}
