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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml bestsellers products report grid block
 *
 * @deprecated after 1.4.0.1
 */
class Mage_Adminhtml_Block_Report_Product_Ordered_Grid extends Mage_Adminhtml_Block_Report_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('gridOrderedProducts');
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('reports/product_ordered_collection');
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    =>Mage::helper('reports')->__('Product Name'),
            'index'     =>'name'
        ));

        $this->addColumn('price', array(
            'header'    =>Mage::helper('reports')->__('Price'),
            'width'     =>'120px',
            'type'      =>'currency',
            'currency_code' => $this->getCurrentCurrencyCode(),
            'index'     =>'price'
        ));

        $this->addColumn('ordered_qty', array(
            'header'    =>Mage::helper('reports')->__('Quantity Ordered'),
            'width'     =>'120px',
            'align'     =>'right',
            'index'     =>'ordered_qty',
            'total'     =>'sum',
            'type'      =>'number'
        ));

        $this->addExportType('*/*/exportOrderedCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportOrderedExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }
}
