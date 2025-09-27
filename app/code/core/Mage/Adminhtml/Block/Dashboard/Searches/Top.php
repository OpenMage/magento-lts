<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml dashboard last search keywords block
 *
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

        $request = $this->getRequest();
        if ($request->getParam('store')) {
            $storeIds = $request->getParam('store');
        } elseif ($request->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($request->getParam('website'))->getStoreIds();
        } elseif ($request->getParam('group')) {
            $storeIds = Mage::app()->getGroup($request->getParam('group'))->getStoreIds();
        } else {
            $storeIds = '';
        }

        $this->_collection
            ->setPopularQueryFilter($storeIds);

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

    /**
     * @param Mage_CatalogSearch_Model_Query $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_search/edit', ['id' => $row->getId()]);
    }
}
