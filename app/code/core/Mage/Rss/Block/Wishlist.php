<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Rss
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer Shared Wishlist Rss Block
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Block_Wishlist extends Mage_Wishlist_Block_Abstract
{
    /**
     * Customer instance
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_rss';

    /**
     * Retrieve Wishlist model
     *
     * @return Mage_Wishlist_Model_Wishlist
     * @throws Exception
     */
    protected function _getWishlist()
    {
        if (is_null($this->_wishlist)) {
            $this->_wishlist = Mage::getModel('wishlist/wishlist');
            $wishlistId = $this->getRequest()->getParam('wishlist_id');
            if ($wishlistId) {
                $this->_wishlist->load($wishlistId);
                if ($this->_wishlist->getCustomerId() != $this->_getCustomer()->getId()) {
                    $this->_wishlist->unsetData();
                }
            } else {
                if($this->_getCustomer()->getId()) {
                    $this->_wishlist->loadByCustomer($this->_getCustomer());
                }
            }
        }
        return $this->_wishlist;
    }

    /**
     * Retrieve Customer instance
     *
     * @return Mage_Customer_Model_Customer
     * @throws Exception
     */
    protected function _getCustomer()
    {
        if (is_null($this->_customer)) {
            $this->_customer = Mage::getModel('customer/customer');

            $params = Mage::helper('core')->urlDecode($this->getRequest()->getParam('data'));
            $data   = explode(',', $params);
            $cId    = abs(intval($data[0]));
            if ($cId && ($cId == Mage::getSingleton('customer/session')->getCustomerId()) ) {
                $this->_customer->load($cId);
            }
        }

        return $this->_customer;
    }

    /**
     * Build wishlist rss feed title
     *
     * @return string
     * @throws Exception
     */
    protected function _getTitle()
    {
        return Mage::helper('rss')->__('%s\'s Wishlist', $this->_getCustomer()->getName());
    }

    /**
     * Render block HTML
     *
     * @return string
     * @throws Mage_Core_Exception
     * @throws Exception
     */
    protected function _toHtml()
    {
        /** @var Mage_Rss_Model_Rss $rssObj */
        $rssObj = Mage::getModel('rss/rss');

        if ($this->_getWishlist()->getId()) {
            $newUrl = Mage::getUrl('wishlist/shared/index', [
                'code'  => $this->_getWishlist()->getSharingCode()
            ]);

            $title  = $this->_getTitle();
            $lang   = Mage::getStoreConfig('general/locale/code');

            $rssObj->_addHeader([
                'title'         => $title,
                'description'   => $title,
                'link'          => $newUrl,
                'charset'       => 'UTF-8',
                'language'      => $lang
            ]);

            /** @var Mage_Wishlist_Model_Item $wishlistItem */
            foreach ($this->getWishlistItems() as $wishlistItem) {
                $product = $wishlistItem->getProduct();
                $productUrl = $this->getProductUrl($product);
                $product->setAllowedInRss(true);
                $product->setAllowedPriceInRss(true);
                $product->setProductUrl($productUrl);
                $args = ['product' => $product];

                Mage::dispatchEvent('rss_wishlist_xml_callback', $args);

                if (!$product->getAllowedInRss()) {
                    continue;
                }

                /** @var Mage_Catalog_Helper_Image $imageHelper */
                $imageHelper = $this->helper('catalog/image');
                /** @var Mage_Catalog_Helper_Output $outputHelper */
                $outputHelper = $this->helper('catalog/output');

                $description = '<table><tr><td><a href="' . $productUrl . '"><img src="'
                    . $imageHelper->init($product, 'thumbnail')->resize(75, 75)
                    . '" border="0" align="left" height="75" width="75"></a></td>'
                    . '<td style="text-decoration:none;">'
                    . $outputHelper->productAttribute($product, $product->getShortDescription(), 'short_description')
                    . '<p>';

                if ($product->getAllowedPriceInRss()) {
                    $description .= $this->getPriceHtml($product,true);
                }
                $description .= '</p>';
                if ($this->hasDescription($product)) {
                    $description .= '<p>' . Mage::helper('wishlist')->__('Comment:')
                        . ' ' . $outputHelper->productAttribute($product, $product->getDescription(), 'description')
                        . '<p>';
                }

                $description .= '</td></tr></table>';

                $rssObj->_addEntry([
                    'title'         => $outputHelper->productAttribute($product, $product->getName(), 'name'),
                    'link'          => $productUrl,
                    'description'   => $description,
                ]);
            }
        }
        else {
            $rssObj->_addHeader([
                'title'         => Mage::helper('rss')->__('Cannot retrieve the wishlist'),
                'description'   => Mage::helper('rss')->__('Cannot retrieve the wishlist'),
                'link'          => Mage::getUrl(),
                'charset'       => 'UTF-8',
            ]);
        }

        return $rssObj->createRssXml();
    }

    /**
     * Retrieve Product View URL
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getProductUrl($product, $additional = [])
    {
        $additional['_rss'] = true;
        return parent::getProductUrl($product, $additional);
    }

    /**
     * Adding customized price template for product type, used as action in layouts
     *
     * @param string $type Catalog Product Type
     * @param string $block Block Type
     * @param string $template Template
     */
    public function addPriceBlockType($type, $block = '', $template = '')
    {
        if ($type) {
            $this->_priceBlockTypes[$type] = [
                'block' => $block,
                'template' => $template
            ];
        }
    }
}
