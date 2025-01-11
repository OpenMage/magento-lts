<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tags Customer Reviews Block
 *
 * @category   Mage
 * @package    Mage_Tag
 */
class Mage_Tag_Block_Customer_Recent extends Mage_Core_Block_Template
{
    /**
     * @var Mage_Tag_Model_Resource_Product_Collection
     */
    protected $_collection;

    protected function _construct()
    {
        parent::_construct();

        $this->_collection = Mage::getModel('tag/tag')->getEntityCollection()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId())
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->setDescOrder()
            ->setPageSize(5)
            ->setActiveFilter()
            ->load()
            ->addProductTags();

        Mage::getSingleton('catalog/product_visibility')
            ->addVisibleInSiteFilterToCollection($this->_collection);
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->_collection->getSize();
    }

    /**
     * @return Mage_Tag_Model_Resource_Product_Collection
     */
    protected function _getCollection()
    {
        return $this->_collection;
    }

    /**
     * @return Mage_Tag_Model_Resource_Product_Collection
     */
    public function getCollection()
    {
        return $this->_getCollection();
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
    public function getAllTagsUrl()
    {
        return Mage::getUrl('tag/customer');
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_collection->getSize() > 0) {
            return parent::_toHtml();
        }
        return '';
    }
}
