<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Child Of Mage_Adminhtml_Block_Tag_Product
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Tag_Model_Resource_Product_Collection getCollection()
 */
class Mage_Adminhtml_Block_Tag_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Mage_Adminhtml_Block_Tag_Product_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_product_grid' . Mage::registry('current_tag')->getId());
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    /**
     * Retrieves Grid Url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/product', ['_current' => true]);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareCollection()
    {
        $tagId = Mage::registry('current_tag')->getId();
        $storeId = Mage::registry('current_tag')->getStoreId();
        $collection = Mage::getModel('tag/tag')
            ->getEntityCollection()
            ->addTagFilter($tagId)
            ->addCustomerFilter(['null' => false])
            ->addStoreFilter($storeId)
            ->addPopularity($tagId);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('product_id', [
            'header'        => Mage::helper('tag')->__('ID'),
            'width'         => '50px',
            'align'         => 'right',
            'index'         => 'entity_id',
        ]);

        $this->addColumn('name', [
            'header'    => Mage::helper('tag')->__('Product Name'),
            'index'     => 'name',
        ]);

        $this->addColumn('popularity', [
            'header'        => Mage::helper('tag')->__('# of Uses'),
            'width'         => '50px',
            'align'         => 'right',
            'index'         => 'popularity',
            'type'          => 'number'
        ]);

        $this->addColumn('sku', [
            'header'    => Mage::helper('tag')->__('SKU'),
            'filter'    => false,
            'sortable'  => false,
            'width'     => 50,
            'align'     => 'right',
            'index'     => 'sku',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getIndex() === 'popularity') {
            $this->getCollection()->addPopularityFilter($column->getFilter()->getCondition());
            return $this;
        }

        return parent::_addColumnFilterToCollection($column);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/catalog_product/edit', ['id' => $row->getProductId()]);
    }
}
