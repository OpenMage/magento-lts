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
 * List of tagged products
 *
 * @category   Mage
 * @package    Mage_Tag
 *
 * @method $this setResultCount(int $value)
 */
class Mage_Tag_Block_Product_Result extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * @var Mage_Tag_Model_Resource_Product_Collection|null
     */
    protected $_productCollection;

    /**
     * @return Mage_Tag_Model_Tag
     */
    public function getTag()
    {
        return Mage::registry('current_tag');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $title = $this->getHeaderText();
        $this->getLayout()->getBlock('head')->setTitle($title);
        $this->getLayout()->getBlock('root')->setHeaderTitle($title);
        return parent::_prepareLayout();
    }

    public function setListOrders()
    {
        $this->getChild('search_result_list')
            ->setAvailableOrders([
                'name' => Mage::helper('tag')->__('Name'),
                'price' => Mage::helper('tag')->__('Price')]);
    }

    public function setListModes()
    {
        $this->getChild('search_result_list')
            ->setModes([
                'grid' => Mage::helper('tag')->__('Grid'),
                'list' => Mage::helper('tag')->__('List')]);
    }

    /**
     * @throws Mage_Core_Model_Store_Exception
     */
    public function setListCollection()
    {
        $this->getChild('search_result_list')
           ->setCollection($this->_getProductCollection());
    }

    /**
     * @return string
     */
    public function getProductListHtml()
    {
        return $this->getChildHtml('search_result_list');
    }

    /**
     * @return Mage_Tag_Model_Resource_Product_Collection
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $tagModel = Mage::getModel('tag/tag');
            $this->_productCollection = $tagModel->getEntityCollection()
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addTagFilter($this->getTag()->getId())
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addAttributeToFilter('status', [
                    'in' => Mage::getSingleton('catalog/product_status')->getSaleableStatusIds()
                ])
                ->addMinimalPrice()
                ->addUrlRewrite()
                ->setActiveFilter();
            Mage::getSingleton('catalog/product_visibility')->addVisibleInSiteFilterToCollection(
                $this->_productCollection
            );
        }

        return $this->_productCollection;
    }

    /**
     * @return int
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getResultCount()
    {
        if (!$this->getData('result_count')) {
            $size = $this->_getProductCollection()->getSize();
            $this->setResultCount($size);
        }
        return $this->getData('result_count');
    }

    /**
     * @return bool|string
     */
    public function getHeaderText()
    {
        if ($this->getTag()->getName()) {
            return Mage::helper('tag')->__("Products tagged with '%s'", $this->escapeHtml($this->getTag()->getName()));
        }
        return false;
    }

    /**
     * @return bool
     */
    public function getSubheaderText()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getNoResultText()
    {
        return Mage::helper('tag')->__('No matches found.');
    }
}
