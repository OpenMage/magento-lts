<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Rss
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review form block
 *
 * @category   Mage
 * @package    Mage_Rss
 */
class Mage_Rss_Block_Catalog_Tag extends Mage_Rss_Block_Catalog_Abstract
{
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $tagModel = Mage::registry('tag_model');
        if ($tagModel) {
            $this->setCacheKey('rss_catalog_tag_' . $this->getStoreId() . '_' . $tagModel->getName());
        }
        $this->setCacheLifetime(600);
    }

    /**
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _toHtml()
    {
        //store id is store view id
        $storeId = $this->_getStoreId();
        $tagModel = Mage::registry('tag_model');
        $newurl = Mage::getUrl('rss/catalog/tag/tagName/' . $tagModel->getName());
        $title = Mage::helper('rss')->__('Products tagged with %s', $tagModel->getName());
        $lang = Mage::getStoreConfig('general/locale/code');

        $rssObj = Mage::getModel('rss/rss');
        $data = ['title' => $title,
            'description' => $title,
            'link'        => $newurl,
            'charset'     => 'UTF-8',
            'language'    => $lang
        ];
        $rssObj->_addHeader($data);

        $collection = $tagModel->getEntityCollection()
            ->addTagFilter($tagModel->getId())
            ->addStoreFilter($storeId);

        $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());

        $product = Mage::getModel('catalog/product');

        /** @var Mage_Core_Model_Resource_Helper_Mysql4 $resourceHelper */
        $resourceHelper = Mage::getResourceHelper('core');
        Mage::getSingleton('core/resource_iterator')->walk(
            $resourceHelper->getQueryUsingAnalyticFunction($collection->getSelect()),
            [[$this, 'addTaggedItemXml']],
            ['rssObj' => $rssObj, 'product' => $product],
            $collection->getSelect()->getAdapter()
        );

        return $rssObj->createRssXml();
    }

    /**
     * Preparing data and adding to rss object
     *
     * @param array $args
     */
    public function addTaggedItemXml($args)
    {
        $product = $args['product'];

        $product->setAllowedInRss(true);
        $product->setAllowedPriceInRss(true);
        Mage::dispatchEvent('rss_catalog_tagged_item_xml_callback', $args);

        if (!$product->getAllowedInRss()) {
            //Skip adding product to RSS
            return;
        }

        $allowedPriceInRss = $product->getAllowedPriceInRss();

        /** @var Mage_Catalog_Helper_Image $helper */
        $helper = $this->helper('catalog/image');

        $product->unsetData()->load($args['row']['entity_id']);
        $description = '<table><tr><td><a href="' . $product->getProductUrl() . '">'
            . '<img src="' . $helper->init($product, 'thumbnail')->resize(75, 75)
            . '" border="0" align="left" height="75" width="75"></a></td>'
            . '<td  style="text-decoration:none;">' . $product->getDescription();

        if ($allowedPriceInRss) {
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
