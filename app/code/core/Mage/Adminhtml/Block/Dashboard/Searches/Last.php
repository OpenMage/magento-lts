<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml dashboard last search keywords block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Dashboard_Searches_Last extends Mage_Adminhtml_Block_Dashboard_Grid
{
    protected $_collection;

    public function __construct()
    {
        parent::__construct();
        $this->setId('lastSearchGrid');
    }

    protected function _prepareCollection()
    {
        if (!$this->isModuleEnabled('Mage_CatalogSearch')) {
            return parent::_prepareCollection();
        }
        $this->_collection = Mage::getModel('catalogsearch/query')
            ->getResourceCollection();
        $this->_collection->setRecentQueryFilter();

        $request = $this->getRequest();
        if ($request->getParam('store')) {
            $this->_collection->addFieldToFilter('store_id', $request->getParam('store'));
        } elseif ($request->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($request->getParam('website'))->getStoreIds();
            $this->_collection->addFieldToFilter('store_id', ['in' => $storeIds]);
        } elseif ($request->getParam('group')) {
            $storeIds = Mage::app()->getGroup($request->getParam('group'))->getStoreIds();
            $this->_collection->addFieldToFilter('store_id', ['in' => $storeIds]);
        }

        $this->setCollection($this->_collection);

        return parent::_prepareCollection();
    }

    /**
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('search_query', [
            'header'    => $this->__('Search Term'),
            'sortable'  => false,
            'index'     => 'query_text',
            'renderer'  => 'adminhtml/dashboard_searches_renderer_searchquery',
        ]);

        $this->addColumn('num_results', [
            'header'    => $this->__('Results'),
            'sortable'  => false,
            'index'     => 'num_results',
            'type'      => 'number',
        ]);

        $this->addColumn('popularity', [
            'header'    => $this->__('Number of Uses'),
            'sortable'  => false,
            'index'     => 'popularity',
            'type'      => 'number',
        ]);

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    /**
     * @param Mage_CatalogSearch_Model_Query $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_search/edit', ['id' => $row->getId()]);
    }
}
