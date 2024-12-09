<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Review
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer Reviews list block
 *
 * @category   Mage
 * @package    Mage_Review
 */
class Mage_Review_Block_Customer_List extends Mage_Customer_Block_Account_Dashboard
{
    /**
     * Product reviews collection
     *
     * @var Mage_Review_Model_Resource_Review_Product_Collection
     */
    protected $_collection;

    /**
     * Initializes collection
     */
    protected function _construct()
    {
        $this->_collection = Mage::getModel('review/review')->getProductCollection();
        $this->_collection
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId())
            ->setDateOrder();
    }

    /**
     * Gets collection items count
     *
     * @return int
     */
    public function count()
    {
        return $this->_collection->getSize();
    }

    /**
     * Get html code for toolbar
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Initializes toolbar
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $toolbar = $this->getLayout()->createBlock('page/html_pager', 'customer_review_list.toolbar')
            ->setCollection($this->getCollection());

        $this->setChild('toolbar', $toolbar);
        return parent::_prepareLayout();
    }

    /**
     * Get collection
     *
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    protected function _getCollection()
    {
        return $this->_collection;
    }

    /**
     * Get collection
     *
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    public function getCollection()
    {
        return $this->_getCollection();
    }

    /**
     * Get review link
     *
     * @return string
     */
    public function getReviewLink()
    {
        return Mage::getUrl('review/customer/view/');
    }

    /**
     * Get product link
     *
     * @return string
     */
    public function getProductLink()
    {
        return Mage::getUrl('catalog/product/view/');
    }

    /**
     * Format date in short format
     *
     * @param string|Zend_Date|null $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $this->_getCollection()
            ->load()
            ->addReviewSummary();
        return parent::_beforeToHtml();
    }
}
