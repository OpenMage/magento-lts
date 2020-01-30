<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     * @return Mage_Adminhtml_Block_Tax_Rule_Grid
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
     * Add filter
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Mage_Adminhtml_Block_Widget_Grid
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
     * Prepare grid columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('code',
            array(
                'header' => Mage::helper('tax')->__('Name'),
                'align' =>'left',
                'index' => 'code',
                'filter_index' => 'code',
            )
        );

        $this->addColumn('customer_tax_classes',
            array(
                'header' => Mage::helper('tax')->__('Customer Tax Class'),
                'sortable'  => false,
                'align' =>'left',
                'index' => 'customer_tax_classes',
                'filter_index' => 'ctc.customer_tax_class_id',
                'type'    => 'options',
                'show_missing_option_values' => true,
                'options' => Mage::getModel('tax/class')->getCollection()
                    ->setClassTypeFilter(Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER)->toOptionHash(),
            )
        );

        $this->addColumn('product_tax_classes',
            array(
                'header' => Mage::helper('tax')->__('Product Tax Class'),
                'sortable'  => false,
                'align' =>'left',
                'index' => 'product_tax_classes',
                'filter_index' => 'ptc.product_tax_class_id',
                'type'    => 'options',
                'show_missing_option_values' => true,
                'options' => Mage::getModel('tax/class')->getCollection()
                    ->setClassTypeFilter(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)->toOptionHash(),
            )
        );

        $this->addColumn('tax_rates',
            array(
                'sortable'  => false,
                'header'  => Mage::helper('tax')->__('Tax Rate'),
                'align'   => 'left',
                'index'   => 'tax_rates',
                'filter_index' => 'rate.tax_calculation_rate_id',
                'type'    => 'options',
                'show_missing_option_values' => true,
                'options' => Mage::getModel('tax/calculation_rate')->getCollection()->toOptionHashOptimized(),
            )
        );

        $this->addColumn('priority',
            array(
                'header' => Mage::helper('tax')->__('Priority'),
                'width' => '50px',
                'index' => 'priority'
            )
        );

        $this->addColumn('calculate_subtotal',
            array(
                'header' => Mage::helper('tax')->__('Subtotal only'),
                'width' => '50px',
                'index' => 'calculate_subtotal'
            )
        );

        $this->addColumn('position',
            array(
                'header' => Mage::helper('tax')->__('Sort Order'),
                'width' => '50px',
                'index' => 'position'
            )
        );

        $actionsUrl = $this->getUrl('*/*/');

        return parent::_prepareColumns();
    }

    /**
     * Return url
     *
     * @param Mage_Core_Model_Abstract $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('rule' => $row->getId()));
    }

}
