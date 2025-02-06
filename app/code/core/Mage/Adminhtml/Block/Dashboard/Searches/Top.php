<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml dashboard last search keywords block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Dashboard_Searches_Top extends Mage_Adminhtml_Block_Dashboard_Grid
{
    protected $_collection;

    public function __construct()
    {
        parent::__construct();
        $this->setId('topSearchGrid');
    }

    protected function _prepareCollection()
    {
        if (!$this->isModuleEnabled('Mage_CatalogSearch')) {
            return parent::_prepareCollection();
        }
        $this->_collection = Mage::getModel('catalogsearch/query')
            ->getResourceCollection();

        if ($this->getRequest()->getParam('store')) {
            $storeIds = $this->getRequest()->getParam('store');
        } elseif ($this->getRequest()->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
        } elseif ($this->getRequest()->getParam('group')) {
            $storeIds = Mage::app()->getGroup($this->getRequest()->getParam('group'))->getStoreIds();
        } else {
            $storeIds = '';
        }

        $this->_collection
            ->setPopularQueryFilter($storeIds);

        $this->setCollection($this->_collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('search_query', [
            'header'    => $this->__('Search Term'),
            'sortable'  => false,
            'index'     => 'name',
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

    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_search/edit', ['id' => $row->getId()]);
    }
}
