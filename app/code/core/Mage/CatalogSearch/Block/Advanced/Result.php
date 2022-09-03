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
 * @package     Mage_CatalogSearch
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Advanced search result
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method setResultCount(int $value)
 */
class Mage_CatalogSearch_Block_Advanced_Result extends Mage_Core_Block_Template
{
    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        /** @var Mage_Page_Block_Html_Breadcrumbs $breadcrumbs */
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb('home', [
                'label'=>Mage::helper('catalogsearch')->__('Home'),
                'title'=>Mage::helper('catalogsearch')->__('Go to Home Page'),
                'link'=>Mage::getBaseUrl()
            ])->addCrumb('search', [
                'label'=>Mage::helper('catalogsearch')->__('Catalog Advanced Search'),
                'link'=>$this->getUrl('*/*/')
            ])->addCrumb('search_result', [
                'label'=>Mage::helper('catalogsearch')->__('Results')
            ]);
        }
        return parent::_prepareLayout();
    }

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
        $this->getChild('search_result_list')
            ->setAvailableOrders($availableOrders)
            ->setSortBy('relevance');
    }

    public function setListModes()
    {
        $this->getChild('search_result_list')
            ->setModes([
                'grid' => Mage::helper('catalogsearch')->__('Grid'),
                'list' => Mage::helper('catalogsearch')->__('List')]);
    }

    public function setListCollection()
    {
        $this->getChild('search_result_list')
           ->setCollection($this->_getProductCollection());
    }

    /**
     * @return Mage_CatalogSearch_Model_Resource_Advanced_Collection
     */
    protected function _getProductCollection()
    {
        return $this->getSearchModel()->getProductCollection();
    }

    /**
     * @return Mage_CatalogSearch_Model_Advanced|Mage_Core_Model_Abstract
     */
    public function getSearchModel()
    {
        return Mage::getSingleton('catalogsearch/advanced');
    }

    /**
     * @return int
     */
    public function getResultCount()
    {
        if (!$this->getData('result_count')) {
            $size = $this->getSearchModel()->getProductCollection()->getSize();
            $this->setResultCount($size);
        }
        return $this->getData('result_count');
    }

    /**
     * @return string
     */
    public function getProductListHtml()
    {
        return $this->getChildHtml('search_result_list');
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getFormUrl()
    {
        return Mage::getModel('core/url')
            ->setQueryParams($this->getRequest()->getQuery())
            ->getUrl('*/*/', ['_escape' => true]);
    }

    /**
     * @return array
     */
    public function getSearchCriterias()
    {
        $searchCriterias = $this->getSearchModel()->getSearchCriterias();
        $middle = ceil(count($searchCriterias) / 2);
        $left = array_slice($searchCriterias, 0, $middle);
        $right = array_slice($searchCriterias, $middle);

        return ['left'=>$left, 'right'=>$right];
    }
}
