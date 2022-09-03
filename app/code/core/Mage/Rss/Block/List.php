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
 * Review form block
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Block_List extends Mage_Core_Block_Template
{
    const XML_PATH_RSS_METHODS = 'rss';

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
                $head->addItem('rss', $feed['url'], 'title="'.$feed['label'].'"');
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
    public function addRssFeed($url, $label, $param = [], $customerGroup=false)
    {
        $param = array_merge($param, ['store_id' => $this->getCurrentStoreId()]);
        if ($customerGroup) {
            $param = array_merge($param, ['cid' => $this->getCurrentCustomerGroupId()]);
        }

        $this->_rssFeeds[] = new Varien_Object(
            [
                'url'   => Mage::getUrl($url, $param),
                'label' => $label
            ]
        );
        return $this;
    }

    public function resetRssFeed()
    {
        $this->_rssFeeds= [];
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
        $this->CategoriesRssFeed();
        return $this->getRssFeeds();
    }

    /**
     * @return array|false
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getRssMiscFeeds()
    {
        $this->resetRssFeed();
        $this->NewProductRssFeed();
        $this->SpecialProductRssFeed();
        $this->SalesRuleProductRssFeed();
        return $this->getRssFeeds();
    }

    /**
     * @throws Mage_Core_Model_Store_Exception
     */
    public function NewProductRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS.'/catalog/new';
        if((bool)Mage::getStoreConfig($path)){
            $this->addRssFeed($path, $this->__('New Products'));
        }
    }

    /**
     * @throws Mage_Core_Model_Store_Exception
     */
    public function SpecialProductRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS.'/catalog/special';
        if((bool)Mage::getStoreConfig($path)){
            $this->addRssFeed($path, $this->__('Special Products'), [],true);
        }
    }

    /**
     * @throws Mage_Core_Model_Store_Exception
     */
    public function SalesRuleProductRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS.'/catalog/salesrule';
        if((bool)Mage::getStoreConfig($path)){
            $this->addRssFeed($path, $this->__('Coupons/Discounts'), [],true);
        }
    }

    /**
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function CategoriesRssFeed()
    {
        $path = self::XML_PATH_RSS_METHODS.'/catalog/category';
        if((bool)Mage::getStoreConfig($path)){
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
                ->addAttributeToFilter('is_active',1)
                ->addIdFilter($nodeIds)
                ->addAttributeToSort('name')
                ->load();

            foreach ($collection as $category) {
                $this->addRssFeed('rss/catalog/category', $category->getName(), ['cid'=>$category->getId()]);
            }
        }
    }
}
