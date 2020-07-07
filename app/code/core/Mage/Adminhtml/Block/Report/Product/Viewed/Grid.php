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
 * Adminhtml most viewed products report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Product_Viewed_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract
{
    /**
     * Column for grid to be grouped by
     *
     * @var string
     */
    protected $_columnGroupBy = 'period';

    /**
     * Grid resource collection name
     *
     * @var string
     */
    protected $_resourceCollectionName  = 'reports/report_product_viewed_collection';

    /**
     * Init grid parameters
     */
    public function __construct()
    {
        parent::__construct();
        $this->setCountTotals(true);
    }

    /**
     * Custom columns preparation
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('period', array(
            'header'        => Mage::helper('adminhtml')->__('Period'),
            'index'         => 'period',
            'width'         => 100,
            'sortable'      => false,
            'period_type'   => $this->getPeriodType(),
            'renderer'      => 'adminhtml/report_sales_grid_column_renderer_date',
            'totals_label'  => Mage::helper('adminhtml')->__('Total'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('product_name', array(
            'header'    => Mage::helper('adminhtml')->__('Product Name'),
            'index'     => 'product_name',
            'type'      => 'string',
            'sortable'  => false
        ));

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('product_price', array(
            'header'        => Mage::helper('adminhtml')->__('Price'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'product_price',
            'sortable'      => false,
            'rate'          => $this->getRate($currencyCode),
        ));

        $this->addColumn('views_num', array(
            'header'    => Mage::helper('adminhtml')->__('Number of Views'),
            'index'     => 'views_num',
            'type'      => 'number',
            'total'     => 'sum',
            'sortable'  => false
        ));


        $this->addExportType('*/*/exportViewedCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportViewedExcel', Mage::helper('adminhtml')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Don't use orders in collection
     *
     * @param Mage_Reports_Model_Resource_Report_Collection_Abstract $collection
     * @param Varien_Object $filterData
     * @return Mage_Adminhtml_Block_Report_Grid_Abstract
     */
    protected function _addOrderStatusFilter($collection, $filterData)
    {
        return $this;
    }
}
