<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Rss
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review form block
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Block_Catalog_New extends Mage_Rss_Block_Catalog_Abstract
{
    protected function _construct()
    {
    }

    /**
     * @return string
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     * @throws Zend_Date_Exception
     */
    protected function _toHtml()
    {
        $storeId = $this->_getStoreId();

        $newurl = Mage::getUrl('rss/catalog/new/store_id/' . $storeId);
        $title = Mage::helper('rss')->__('New Products from %s', Mage::app()->getStore()->getGroup()->getName());
        $lang = Mage::getStoreConfig('general/locale/code');

        $rssObj = Mage::getModel('rss/rss');
        $data = [
            'title'       => $title,
            'description' => $title,
            'link'        => $newurl,
            'charset'     => 'UTF-8',
            'language'    => $lang
        ];
        $rssObj->_addHeader($data);

        $product = Mage::getModel('catalog/product');

        $todayStartOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('00:00:00')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $todayEndOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('23:59:59')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $products = $product->getCollection()
            ->setStoreId($storeId)
            ->addStoreFilter()
            ->addAttributeToFilter('news_from_date', ['or' => [
                0 => ['date' => true, 'to' => $todayEndOfDayDate],
                1 => ['is' => new Zend_Db_Expr('null')]]
            ], 'left')
            ->addAttributeToFilter('news_to_date', ['or' => [
                0 => ['date' => true, 'from' => $todayStartOfDayDate],
                1 => ['is' => new Zend_Db_Expr('null')]]
            ], 'left')
            ->addAttributeToFilter(
                [
                    ['attribute' => 'news_from_date', 'is' => new Zend_Db_Expr('not null')],
                    ['attribute' => 'news_to_date', 'is' => new Zend_Db_Expr('not null')]
                ]
            )
            ->addAttributeToSort('news_from_date', 'desc')
            ->addAttributeToSelect(['name', 'short_description', 'description', 'thumbnail'], 'inner')
            ->addAttributeToSelect(
                [
                    'price', 'special_price', 'special_from_date', 'special_to_date',
                    'msrp_enabled', 'msrp_display_actual_price_type', 'msrp'
                ],
                'left'
            )
            ->applyFrontendPriceLimitations()
        ;

        $products->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());

        /*
        using resource iterator to load the data one by one
        instead of loading all at the same time. loading all data at the same time can cause the big memory allocation.
        */
        Mage::getSingleton('core/resource_iterator')->walk(
            $products->getSelect(),
            [[$this, 'addNewItemXmlCallback']],
            ['rssObj' => $rssObj, 'product' => $product]
        );

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
        Mage::dispatchEvent('rss_catalog_new_xml_callback', $args);

        if (!$product->getAllowedInRss()) {
            //Skip adding product to RSS
            return;
        }

        $allowedPriceInRss = $product->getAllowedPriceInRss();

        /** @var Mage_Catalog_Helper_Image $helper */
        $helper = $this->helper('catalog/image');

        $product->setData($args['row']);
        $description = '<table><tr>'
            . '<td><a href="' . $product->getProductUrl() . '"><img src="'
            . $helper->init($product, 'thumbnail')->resize(75, 75)
            . '" border="0" align="left" height="75" width="75"></a></td>' .
            '<td  style="text-decoration:none;">' . $product->getDescription();

        if ($allowedPriceInRss) {
            $description .= $this->getPriceHtml($product, true);
        }

        $description .= '</td>' .
            '</tr></table>';

        $rssObj = $args['rssObj'];
        $data = [
                'title'         => $product->getName(),
                'link'          => $product->getProductUrl(),
                'description'   => $description,
        ];
        $rssObj->_addEntry($data);
    }
}
