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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml items in carts report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Shopcart_Customer_Grid extends Mage_Adminhtml_Block_Report_Grid_Shopcart
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('grid');
    }

    protected function _prepareCollection()
    {
        //TODO: add full name logic
        $collection = Mage::getResourceModel('reports/customer_collection')
          ->addAttributeToSelect('firstname')
          ->addAttributeToSelect('lastname');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->addCartInfo();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    =>Mage::helper('reports')->__('ID'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'entity_id'
        ));

        $this->addColumn('firstname', array(
            'header'    =>Mage::helper('reports')->__('First Name'),
            'index'     =>'firstname'
        ));

        $this->addColumn('lastname', array(
            'header'    =>Mage::helper('reports')->__('Last Name'),
            'index'     =>'lastname'
        ));

        $this->addColumn('items', array(
            'header'    =>Mage::helper('reports')->__('Items in Cart'),
            'width'     =>'70px',
            'sortable'  =>false,
            'align'     =>'right',
            'index'     =>'items'
        ));

        $currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('total', array(
            'header'    =>Mage::helper('reports')->__('Total'),
            'width'     =>'70px',
            'sortable'  =>false,
            'type'      =>'currency',
            'align'     =>'right',
            'currency_code' => $currencyCode,
            'index'     =>'total',
            'renderer'  =>'adminhtml/report_grid_column_renderer_currency',
            'rate'          => $this->getRate($currencyCode),
        ));

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportCustomerCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportCustomerExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }

}
