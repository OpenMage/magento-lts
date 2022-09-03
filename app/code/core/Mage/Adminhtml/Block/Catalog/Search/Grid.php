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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category    Mage
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
        $this->addColumn('query_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'width'     => '50px',
            'index'     => 'query_id',
        ));

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
                'sortable'      => false
            ]);
        }

        $this->addColumn('num_results', [
            'header'    => Mage::helper('catalog')->__('Results'),
            'index'     => 'num_results',
            'type'      => 'number'
        ]);

        $this->addColumn('popularity', [
            'header'    => Mage::helper('catalog')->__('Number of Uses'),
            'index'     => 'popularity',
            'type'      => 'number'
        ]);

        $this->addColumn('synonym_for', [
            'header'    => Mage::helper('catalog')->__('Synonym For'),
            'align'     => 'left',
            'index'     => 'synonym_for',
            'width'     => '160px'
        ]);

        $this->addColumn('redirect', [
            'header'    => Mage::helper('catalog')->__('Redirect'),
            'align'     => 'left',
            'index'     => 'redirect',
            'width'     => '200px'
        ]);

        $this->addColumn('display_in_terms', [
            'header'=>Mage::helper('catalog')->__('Display in Suggested Terms'),
            'sortable'=>true,
            'index'=>'display_in_terms',
            'type' => 'options',
            'width' => '100px',
            'options' => [
                '1' => Mage::helper('catalog')->__('Yes'),
                '0' => Mage::helper('catalog')->__('No'),
            ],
            'align' => 'left',
        ]);
        $this->addColumn('action',
            [
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => [[
                    'caption'   => Mage::helper('catalog')->__('Edit'),
                    'url'       => [
                        'base'=>'*/*/edit'
                    ],
                    'field'   => 'id'
                ]],
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'catalog',
            ]);
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

        $this->getMassactionBlock()->addItem('delete', [
             'label'    => Mage::helper('catalog')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('catalog')->__('Are you sure?')
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
