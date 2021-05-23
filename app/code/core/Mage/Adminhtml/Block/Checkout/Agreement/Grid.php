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

/**
 * Class Mage_Adminhtml_Block_Checkout_Agreement_Grid
 *
 * @method Mage_Checkout_Model_Resource_Agreement_Collection getCollection()
 */
class Mage_Adminhtml_Block_Checkout_Agreement_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Mage_Adminhtml_Block_Checkout_Agreement_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('agreement_id');
        $this->setId('agreementGrid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @inheritDoc
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('checkout/agreement')
            ->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('agreement_id',
            array(
                'header'=>Mage::helper('checkout')->__('ID'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'agreement_id'
            )
        );

        $this->addColumn('position', array(
            'header'    => Mage::helper('adminhtml')->__('Position'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'position',
            'type'      => 'text',
        ));

        $this->addColumn('name',
            array(
                'header'=>Mage::helper('checkout')->__('Condition Name'),
                'index' => 'name'
            )
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('adminhtml')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
            ));
        }

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('adminhtml')->__('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                0 => Mage::helper('adminhtml')->__('Disabled'),
                1 => Mage::helper('adminhtml')->__('Enabled')
            ),
        ));

        return parent::_prepareColumns();
    }

    /**
     * @return void
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * @param $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

    /**
     * @param Mage_Checkout_Model_Agreement $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
