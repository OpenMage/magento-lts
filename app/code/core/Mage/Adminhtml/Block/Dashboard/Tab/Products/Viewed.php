<?php
/**
 * Adminhtml dashboard most viewed products grid
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Dashboard_Tab_Products_Viewed extends Mage_Adminhtml_Block_Dashboard_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('productsReviewedGrid');
    }

    protected function _prepareCollection()
    {
        if ($this->getParam('website')) {
            $storeIds = Mage::app()->getWebsite($this->getParam('website'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } elseif ($this->getParam('group')) {
            $storeIds = Mage::app()->getGroup($this->getParam('group'))->getStoreIds();
            $storeId = array_pop($storeIds);
        } else {
            $storeId = (int) $this->getParam('store');
        }
        $collection = Mage::getResourceModel('reports/product_collection')
            ->addAttributeToSelect('*')
            ->addViewsCount()
            ->setStoreId($storeId)
            ->addStoreFilter($storeId);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', [
            'header'    => Mage::helper('reports')->__('Product Name'),
            'sortable'  => false,
            'index'     => 'name',
        ]);

        $this->addColumn('price', [
            'type'      => 'currency',
            'currency_code' => (string) Mage::app()->getStore((int) $this->getParam('store'))->getBaseCurrencyCode(),
            'sortable'  => false,
        ]);

        $this->addColumn('views', [
            'header'    => Mage::helper('reports')->__('Number of Views'),
            'width'     => '120px',
            'align'     => 'right',
            'sortable'  => false,
            'index'     => 'views',
        ]);

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        $params = ['id' => $row->getId()];
        if ($this->getRequest()->getParam('store')) {
            $params['store'] = $this->getRequest()->getParam('store');
        }
        return $this->getUrl('*/catalog_product/edit', $params);
    }
}
