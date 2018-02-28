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
 * @package     Mage_Tag
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * List of products tagged by customer Block
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Block_Customer_View extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Tagged Product Collection
     *
     * @var Mage_Tag_Model_Mysql4_Product_Collection
     */
    protected $_collection;

    /**
     * Current Tag object
     *
     * @var Mage_Tag_Model_Tag
     */
    protected $_tagInfo;

    /**
     * Initialize block
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTagId(Mage::registry('tagId'));
    }

    /**
     * Retrieve current Tag object
     *
     * @return Mage_Tag_Model_Tag
     */
    public function getTagInfo()
    {
        if (is_null($this->_tagInfo)) {
            $this->_tagInfo = Mage::getModel('tag/tag')
                ->load($this->getTagId());
        }
        return $this->_tagInfo;
    }

    /**
     * Retrieve Tagged Product Collection items
     *
     * @return array
     */
    public function getMyProducts()
    {
        return $this->_getCollection()->getItems();
    }

    /**
     * Retrieve count of Tagged Product(s)
     *
     * @return int
     */
    public function getCount()
    {
        return sizeof($this->getMyProducts());
    }

    /**
     * Retrieve Product Info URL
     *
     * @param int $productId
     * @return string
     */
    public function getReviewUrl($productId)
    {
        return Mage::getUrl('review/product/list', array('id' => $productId));
    }

    /**
     * Preparing block layout
     *
     * @return Mage_Tag_Block_Customer_View
     */
    protected function _prepareLayout()
    {
        $toolbar = $this->getLayout()
            ->createBlock('page/html_pager', 'customer_tag_list.toolbar')
            ->setCollection($this->_getCollection());

        $this->setChild('toolbar', $toolbar);
        return parent::_prepareLayout();
    }

    /**
     * Retrieve Toolbar block HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Retrieve Current Mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }

    /**
     * Retrieve Tagged product(s) collection
     *
     * @return Mage_Tag_Model_Mysql4_Product_Collection
     */
    protected function _getCollection()
    {
        if (is_null($this->_collection)) {
            $this->_collection = Mage::getModel('tag/tag')
                ->getEntityCollection()
                ->addTagFilter($this->getTagId())
                ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId())
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->setActiveFilter();

            Mage::getSingleton('catalog/product_status')
                ->addVisibleFilterToCollection($this->_collection);
            Mage::getSingleton('catalog/product_visibility')
                ->addVisibleInSiteFilterToCollection($this->_collection);
        }
        return $this->_collection;
    }
}
