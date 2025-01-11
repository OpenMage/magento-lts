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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review form block
 *
 * @category   Mage
 * @package    Mage_Rss
 */
class Mage_Rss_Block_List extends Mage_Core_Block_Template
{
    public const XML_PATH_RSS_METHODS = 'rss';

    protected $_rssFeeds = [];

    /**
     * Add Link elements to head
     *
     * @return $this
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _prepareLayout()
    {
        /** @var Mage_Page_Block_Html_Head $head */
        $head   = $this->getLayout()->getBlock('head');
        $feeds  = $this->getRssMiscFeeds();
        if ($head && !empty($feeds)) {
            foreach ($feeds as $feed) {
                $head->addItem('rss', $feed['url'], 'title="' . $feed['label'] . '"');
            }
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrieve rss feeds
     *
     * @return array|false
     */
    public function getRssFeeds()
    {
        return empty($this->_rssFeeds) ? false : $this->_rssFeeds;
    }

    /**
     * Add new rss feed
     *
     * @param string $url
     * @param string $label
     * @param array $param
     * @param bool $customerGroup
     * @return $this
     * @throws Mage_Core_Model_Store_Exception
     */
    public function addRssFeed($url, $label, $param = [], $customerGroup = false)
    {
        $param = array_merge($param, ['store_id' => $this->getCurrentStoreId()]);
        if ($customerGroup) {
            $param = array_merge($param, ['cid' => $this->getCurrentCustomerGroupId()]);
        }

        $this->_rssFeeds[] = new Varien_Object(
            [
                'url'   => Mage::getUrl($url, $param),
                'label' => $label,
            ],
        );
        return $this;
    }

    public function resetRssFeed()
    {
        $this->_rssFeeds = [];
    }

    /**
     * @return int
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCurrentStoreId()
    {
        return Mage::app()->getStore()->getId();
    }

    /**
     * @return int
     */
    public function getCurrentCustomerGroupId()
    {
        return Mage::getSingleton('customer/session')->getCustomerGroupId();
    }

    /**
     * Retrieve rss catalog feeds
     *
     * array structure:
     *
     * @return  array
     */
    public function getRssCatalogFeeds()
    {
        $this->resetRssFeed();
        $this->categoriesRssFeed();
        return $this->getRssFeeds();
    }

    /**
     * @return array|false
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getRssMiscFeeds()
    {
        $this->resetRssFeed();
        $this->newProductRssFeed();
        $this->specialProductRssFeed();
        $this->salesRuleProductRssFeed();
        return $this->getRssFeeds();
    }

    /**
     * @throws Mage_Core_Model_Store_Exception
     */
    public function newProductRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS . '/catalog/new';
        if (Mage::getStoreConfigFlag($path)) {
            $this->addRssFeed($path, $this->__('New Products'));
        }
    }

    /**
     * @throws Mage_Core_Model_Store_Exception
     */
    public function specialProductRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS . '/catalog/special';
        if (Mage::getStoreConfigFlag($path)) {
            $this->addRssFeed($path, $this->__('Special Products'), [], true);
        }
    }

    /**
     * @throws Mage_Core_Model_Store_Exception
     */
    public function salesRuleProductRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS . '/catalog/salesrule';
        if (Mage::getStoreConfigFlag($path)) {
            $this->addRssFeed($path, $this->__('Coupons/Discounts'), [], true);
        }
    }

    /**
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function categoriesRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS . '/catalog/category';
        if (Mage::getStoreConfigFlag($path)) {
            $category = Mage::getModel('catalog/category');

            /** @var Varien_Data_Tree_Node $treeModel */
            $treeModel = $category->getTreeModel()->loadNode(Mage::app()->getStore()->getRootCategoryId());
            $nodes = $treeModel->loadChildren()->getChildren();

            $nodeIds = [];
            foreach ($nodes as $node) {
                $nodeIds[] = $node->getId();
            }

            $collection = $category->getCollection()
                ->addAttributeToSelect('url_key')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('is_anchor')
                ->addAttributeToFilter('is_active', 1)
                ->addIdFilter($nodeIds)
                ->addAttributeToSort('name')
                ->load();

            foreach ($collection as $category) {
                $this->addRssFeed('rss/catalog/category', $category->getName(), ['cid' => $category->getId()]);
            }
        }
    }
}
