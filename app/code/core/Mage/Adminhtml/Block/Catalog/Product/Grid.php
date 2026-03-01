<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

use Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract as MassAction;

/**
 * Adminhtml customer grid block
 *
 * @package    Mage_Adminhtml
 *
 * @method Mage_Catalog_Model_Resource_Product_Collection getCollection()
 */
class Mage_Adminhtml_Block_Catalog_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id');

        if ($this->isModuleEnabled('Mage_CatalogInventory', 'catalog')) {
            $collection->joinField(
                'qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left',
            );
        }

        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $adminStore,
            );
            $collection->joinAttribute(
                'custom_name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId(),
            );
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId(),
            );
            $collection->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner',
                $store->getId(),
            );
            $collection->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left',
                $store->getId(),
            );
        } else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }

        $this->setCollection($collection);

        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() === 'websites') {
                $this->getCollection()->joinField(
                    'websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left',
                );
            }
        }

        return parent::_addColumnFilterToCollection($column);
    }

    /**
     * @inheritDoc
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => Mage::helper('catalog')->__('ID'),
                'index' => 'entity_id',
            ],
        );
        $this->addColumn(
            'name',
            [
                'header' => Mage::helper('catalog')->__('Name'),
                'width' => '300px',
                'index' => 'name',
            ],
        );

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn(
                'custom_name',
                [
                    'header' => Mage::helper('catalog')->__('Name in %s', $this->escapeHtml($store->getName())),
                    'index' => 'custom_name',
                ],
            );
        }

        $this->addColumn(
            'type',
            [
                'header' => Mage::helper('catalog')->__('Type'),
                'width' => '150px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            ],
        );

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->setOrder('attribute_set_name', 'asc')
            ->load()
            ->toOptionHash();

        $this->addColumn(
            'set_name',
            [
                'header' => Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '150px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
            ],
        );

        $this->addColumn(
            'sku',
            [
                'header' => Mage::helper('catalog')->__('SKU'),
                'width' => '150px',
                'index' => 'sku',
            ],
        );

        $store = $this->_getStore();
        $this->addColumn(
            'price',
            [
                'type'          => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
            ],
        );

        if ($this->isModuleEnabled('Mage_CatalogInventory', 'catalog')) {
            $this->addColumn(
                'qty',
                [
                    'header' => Mage::helper('catalog')->__('Qty'),
                    'width' => '100px',
                    'type'  => 'number',
                    'index' => 'qty',
                ],
            );
        }

        $this->addColumn(
            'visibility',
            [
                'header' => Mage::helper('catalog')->__('Visibility'),
                'width' => '150px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
            ],
        );

        $this->addColumn(
            'status',
            [
                'header' => Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
            ],
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
                'websites',
                [
                    'header' => Mage::helper('catalog')->__('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
                ],
            );
        }

        $this->addColumn(
            'action',
            [
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => [
                    [
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => [
                            'base' => '*/*/edit',
                            'params' => ['store' => $this->getRequest()->getParam('store')],
                        ],
                        'field'   => 'id',
                    ],
                ],

                'index'     => 'stores',
            ],
        );

        if ($this->isModuleEnabled('Mage_Rss', 'catalog')
            && Mage::helper('rss')->isRssAdminCatalogNotifyStockEnabled()
        ) {
            $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));
        }

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem(MassAction::DELETE, [
            'label' => Mage::helper('catalog')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete'),
        ]);

        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();

        array_unshift($statuses, ['label' => '', 'value' => '']);
        $this->getMassactionBlock()->addItem(MassAction::STATUS, [
            'label' => Mage::helper('catalog')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', ['_current' => true]),
            'additional' => [
                'visibility' => [
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('catalog')->__('Status'),
                    'values' => $statuses,
                ],
            ],
        ]);

        if (Mage::getSingleton('admin/session')->isAllowed('catalog/update_attributes')) {
            $this->getMassactionBlock()->addItem(MassAction::ATTRIBUTES, [
                'label' => Mage::helper('catalog')->__('Update Attributes'),
                'url'   => $this->getUrl('*/catalog_product_action_attribute/edit', ['_current' => true]),
            ]);
        }

        Mage::dispatchEvent('adminhtml_catalog_product_grid_prepare_massaction', ['block' => $this]);
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * @param  Mage_Catalog_Model_Product $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', [
            'store' => $this->getRequest()->getParam('store'),
            'id' => $row->getId()]);
    }
}
