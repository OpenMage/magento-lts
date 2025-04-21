<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

use Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract as MassAction;

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Search_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init Grid default properties
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('catalog_search_grid');
        $this->setDefaultSort('query_id');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare collection for Grid
     *
     * @inheritDoc
     * @throws Mage_Core_Exception
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalogsearch/query')
            ->getResourceCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('query_id', [
            'header'    => Mage::helper('catalog')->__('ID'),
            'width'     => '50px',
            'index'     => 'query_id',
        ]);

        $this->addColumn('search_query', [
            'header'    => Mage::helper('catalog')->__('Search Query'),
            'index'     => 'query_text',
        ]);

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', [
                'header'        => Mage::helper('catalog')->__('Store'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_view'    => true,
                'sortable'      => false,
            ]);
        }

        $this->addColumn('num_results', [
            'header'    => Mage::helper('catalog')->__('Results'),
            'index'     => 'num_results',
            'type'      => 'number',
        ]);

        $this->addColumn('popularity', [
            'header'    => Mage::helper('catalog')->__('Number of Uses'),
            'index'     => 'popularity',
            'type'      => 'number',
        ]);

        $this->addColumn('synonym_for', [
            'header'    => Mage::helper('catalog')->__('Synonym For'),
            'align'     => 'left',
            'index'     => 'synonym_for',
            'width'     => '160px',
        ]);

        $this->addColumn('redirect', [
            'header'    => Mage::helper('catalog')->__('Redirect'),
            'align'     => 'left',
            'index'     => 'redirect',
            'width'     => '200px',
        ]);

        $this->addColumn('display_in_terms', [
            'header' => Mage::helper('catalog')->__('Display in Suggested Terms'),
            'index' => 'display_in_terms',
            'type' => 'options',
            'width' => '100px',
            'options' => [
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ],
            'align' => 'left',
        ]);
        $this->addColumn(
            'action',
            [
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => [[
                    'caption'   => Mage::helper('catalog')->__('Edit'),
                    'url'       => [
                        'base' => '*/*/edit',
                    ],
                    'field'   => 'id',
                ]],
                'index'     => 'catalog',
            ],
        );
        return parent::_prepareColumns();
    }

    /**
     * Prepare grid massaction actions
     *
     * @inheritDoc
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('query_id');
        $this->getMassactionBlock()->setFormFieldName('search');

        $this->getMassactionBlock()->addItem(MassAction::DELETE, [
            'label'    => Mage::helper('catalog')->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
        ]);

        return parent::_prepareMassaction();
    }

    /**
     * Retrieve Row Click callback URL
     *
     * @param Mage_CatalogSearch_Model_Query $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
