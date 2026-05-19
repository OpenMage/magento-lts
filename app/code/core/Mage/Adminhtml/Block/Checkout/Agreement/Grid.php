<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Class Mage_Adminhtml_Block_Checkout_Agreement_Grid
 *
 * @package    Mage_Adminhtml
 *
 * @method Mage_Checkout_Model_Resource_Agreement_Collection getCollection()
 */
class Mage_Adminhtml_Block_Checkout_Agreement_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected string $_eventPrefix = 'adminhtml_checkout_agreement_grid';

    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('agreement_id');
        $this->setId('agreementGrid');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('checkout/agreement')
            ->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    #[Override]
    protected function _prepareColumns()
    {
        $this->addColumn(
            'agreement_id',
            [
                'header' => Mage::helper('checkout')->__('ID'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'agreement_id',
            ],
        );

        $this->addColumn('position', [
            'header'    => Mage::helper('adminhtml')->__('Position'),
            'align'     => 'right',
            'width'     => '50px',
            'index'     => 'position',
            'type'      => 'text',
        ]);

        $this->addColumn(
            'name',
            [
                'header' => Mage::helper('checkout')->__('Condition Name'),
                'index' => 'name',
            ],
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', [
                'header'        => Mage::helper('adminhtml')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback' => $this->_filterStoreCondition(...),
            ]);
        }

        $this->addColumn('is_active', [
            'header'    => Mage::helper('adminhtml')->__('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => [
                0 => Mage::helper('adminhtml')->__('Disabled'),
                1 => Mage::helper('adminhtml')->__('Enabled'),
            ],
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        return parent::_afterLoadCollection();
    }

    /**
     * @param Mage_Checkout_Model_Resource_Agreement_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column           $column
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if ($value = $column->getFilter()->getValue()) {
            $collection->addStoreFilter($value);
        }
    }

    /**
     * @inheritDoc
     * @param  Mage_Checkout_Model_Agreement $row
     * @throws Mage_Core_Exception
     */
    #[Override]
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }
}
