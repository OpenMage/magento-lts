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
 * Recent Customer Reviews Block
 *
 * @category   Mage
 * @package    Mage_Review
 */
class Mage_Review_Block_Customer_Recent extends Mage_Core_Block_Template
{
    /**
     * @var Mage_Review_Model_Resource_Review_Product_Collection
     */
    protected $_collection;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('review/customer/list.phtml');

        $this->_collection = Mage::getModel('review/review')->getProductCollection();

        $this->_collection
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId())
            ->setDateOrder()
            ->setPageSize(5)
            ->load()
            ->addReviewSummary();
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->_collection->getSize();
    }

    /**
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    protected function _getCollection()
    {
        return $this->_collection;
    }

    /**
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    public function getCollection()
    {
        return $this->_getCollection();
    }

    /**
     * @return string
     */
    public function getReviewLink()
    {
        return Mage::getUrl('review/customer/view/');
    }

    /**
     * @return string
     */
    public function getProductLink()
    {
        return Mage::getUrl('catalog/product/view/');
    }

    /**
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }

    /**
     * @return string
     */
    public function getAllReviewsUrl()
    {
        return Mage::getUrl('review/customer');
    }

    /**
     * @param int $id
     * @return string
     */
    public function getReviewUrl($id)
    {
        return Mage::getUrl('review/customer/view', ['id' => $id]);
    }
}
