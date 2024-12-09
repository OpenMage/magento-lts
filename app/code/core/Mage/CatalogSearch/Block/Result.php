<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product search result block
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @module     Catalog
 *
 * @method $this setResultCount(int $value)
 */
class Mage_CatalogSearch_Block_Result extends Mage_Core_Block_Template
{
    /**
     * Catalog Product collection
     *
     * @var Mage_CatalogSearch_Model_Resource_Fulltext_Collection|Mage_Eav_Model_Entity_Collection_Abstract|null
     */
    protected $_productCollection;

    /**
     * Retrieve query model object
     *
     * @return Mage_CatalogSearch_Model_Query
     */
    protected function _getQuery()
    {
        /** @var Mage_CatalogSearch_Helper_Data $helper */
        $helper = $this->helper('catalogsearch');
        return $helper->getQuery();
    }

    /**
     * Prepare layout
     *
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        /** @var Mage_CatalogSearch_Helper_Data $helper */
        $helper = $this->helper('catalogsearch');

        // add Home breadcrumb
        /** @var Mage_Page_Block_Html_Breadcrumbs $breadcrumbs */
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $title = $this->__("Search results for: '%s'", $helper->getQueryText());

            $breadcrumbs->addCrumb('home', [
                'label' => $this->__('Home'),
                'title' => $this->__('Go to Home Page'),
                'link'  => Mage::getBaseUrl()
            ])->addCrumb('search', [
                'label' => $title,
                'title' => $title
            ]);
        }

        // modify page title
        $title = $this->__("Search results for: '%s'", $helper->getEscapedQueryText());
        $this->getLayout()->getBlock('head')->setTitle($title);

        return parent::_prepareLayout();
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getLayout()->getBlock('search_result_list')->getChildHtml('additional');
    }

    /**
     * Retrieve search list toolbar block
     *
     * @return Mage_Catalog_Block_Product_List
     */
    public function getListBlock()
    {
        return $this->getChild('search_result_list');
    }

    /**
     * Set search available list orders
     *
     * @return $this
     */
    public function setListOrders()
    {
        $category = Mage::getSingleton('catalog/layer')
            ->getCurrentCategory();
        /** @var Mage_Catalog_Model_Category $category */
        $availableOrders = $category->getAvailableSortByOptions();
        unset($availableOrders['position']);
        $availableOrders = array_merge([
            'relevance' => $this->__('Relevance')
        ], $availableOrders);

        $this->getListBlock()
            ->setAvailableOrders($availableOrders)
            ->setDefaultDirection('desc')
            ->setSortBy('relevance');

        return $this;
    }

    /**
     * Set available view mode
     *
     * @return $this
     */
    public function setListModes()
    {
        $this->getListBlock()
            ->setModes([
                'grid' => $this->__('Grid'),
                'list' => $this->__('List')]);
        return $this;
    }

    /**
     * Set Search Result collection
     *
     * @return $this
     */
    public function setListCollection()
    {
        return $this;
    }

    /**
     * Retrieve Search result list HTML output
     *
     * @return string
     */
    public function getProductListHtml()
    {
        return $this->getChildHtml('search_result_list');
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_CatalogSearch_Model_Resource_Fulltext_Collection|Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $this->_productCollection = $this->getListBlock()->getLoadedProductCollection();
        }

        return $this->_productCollection;
    }

    /**
     * Retrieve search result count
     *
     * @return string
     */
    public function getResultCount()
    {
        if (!$this->getData('result_count')) {
            $size = $this->_getProductCollection()->getSize();
            $this->_getQuery()->setNumResults($size);
            $this->setResultCount($size);
        }
        return $this->getData('result_count');
    }

    /**
     * Retrieve No Result or Minimum query length Text
     *
     * @return string
     */
    public function getNoResultText()
    {
        if (Mage::helper('catalogsearch')->isMinQueryLength()) {
            return Mage::helper('catalogsearch')->__(
                'Minimum Search query length is %s',
                $this->_getQuery()->getMinQueryLength()
            );
        }
        return $this->_getData('no_result_text');
    }

    /**
     * Retrieve Note messages
     *
     * @return array
     */
    public function getNoteMessages()
    {
        return Mage::helper('catalogsearch')->getNoteMessages();
    }
}
