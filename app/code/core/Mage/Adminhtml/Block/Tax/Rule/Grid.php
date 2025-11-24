<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Class Mage_Adminhtml_Block_Tax_Rule_Grid
 *
 * @package    Mage_Adminhtml
 *
 * @method Mage_Tax_Model_Resource_Calculation_Rule_Collection getCollection()
 */
class Mage_Adminhtml_Block_Tax_Rule_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set default value
     */
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('tax_rule_id');
        $this->setId('taxRuleGrid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare grid collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tax/calculation_rule')
            ->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        if ($this->getCollection()) {
            $this->getCollection()
                ->addCustomerTaxClassesToResult()
                ->addProductTaxClassesToResult()
                ->addRatesToResult();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            switch ($column->getId()) {
                case 'tax_rates':
                    $this->getCollection()->joinCalculationData('rate');
                    break;

                case 'customer_tax_classes':
                    $this->getCollection()->joinCalculationData('ctc');
                    break;

                case 'product_tax_classes':
                    $this->getCollection()->joinCalculationData('ptc');
                    break;
            }
        }

        return parent::_addColumnFilterToCollection($column);
    }

    /**
     * @inheritDoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'code',
            [
                'header' => Mage::helper('tax')->__('Name'),
                'align' => 'left',
                'index' => 'code',
                'filter_index' => 'code',
            ],
        );

        $this->addColumn(
            'customer_tax_classes',
            [
                'header' => Mage::helper('tax')->__('Customer Tax Class'),
                'sortable'  => false,
                'align' => 'left',
                'index' => 'customer_tax_classes',
                'filter_index' => 'ctc.customer_tax_class_id',
                'type'    => 'options',
                'show_missing_option_values' => true,
                'options' => Mage::getModel('tax/class')->getCollection()
                    ->setClassTypeFilter(Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER)->toOptionHash(),
            ],
        );

        $this->addColumn(
            'product_tax_classes',
            [
                'header' => Mage::helper('tax')->__('Product Tax Class'),
                'sortable'  => false,
                'align' => 'left',
                'index' => 'product_tax_classes',
                'filter_index' => 'ptc.product_tax_class_id',
                'type'    => 'options',
                'show_missing_option_values' => true,
                'options' => Mage::getModel('tax/class')->getCollection()
                    ->setClassTypeFilter(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)->toOptionHash(),
            ],
        );

        $this->addColumn(
            'tax_rates',
            [
                'sortable'  => false,
                'header'  => Mage::helper('tax')->__('Tax Rate'),
                'align'   => 'left',
                'index'   => 'tax_rates',
                'filter_index' => 'rate.tax_calculation_rate_id',
                'type'    => 'options',
                'show_missing_option_values' => true,
                'options' => Mage::getModel('tax/calculation_rate')->getCollection()->toOptionHashOptimized(),
            ],
        );

        $this->addColumn(
            'priority',
            [
                'header' => Mage::helper('tax')->__('Priority'),
                'width' => '50px',
                'index' => 'priority',
            ],
        );

        $this->addColumn(
            'calculate_subtotal',
            [
                'header' => Mage::helper('tax')->__('Subtotal only'),
                'width' => '50px',
                'index' => 'calculate_subtotal',
            ],
        );

        $this->addColumn(
            'position',
            [
                'header' => Mage::helper('tax')->__('Sort Order'),
                'width' => '50px',
                'index' => 'position',
            ],
        );

        $actionsUrl = $this->getUrl('*/*/');

        return parent::_prepareColumns();
    }

    /**
     * Return url
     *
     * @param Mage_Tax_Model_Calculation_Rule $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['rule' => $row->getId()]);
    }
}
