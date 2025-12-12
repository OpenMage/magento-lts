<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

use Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract as MassAction;

/**
 * Adminhtml sales report grid block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Refresh_Statistics_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setUseAjax(false);
    }

    /**
     * @param string $reportCode
     * @return string
     * @throws Zend_Date_Exception
     */
    protected function _getUpdatedAt($reportCode)
    {
        $flag = Mage::getModel('reports/flag')->setReportFlagCode($reportCode)->loadSelf();
        return ($flag->hasData())
            ? Mage::app()->getLocale()->storeDate(
                0,
                new Zend_Date($flag->getLastUpdate(), Varien_Date::DATETIME_INTERNAL_FORMAT),
                true,
            )
            : '';
    }

    /**
     * @inheritDoc
     */
    protected function _prepareCollection()
    {
        $collection = new Varien_Data_Collection();

        $data = [
            [
                'id'            => 'sales',
                'report'        => Mage::helper('sales')->__('Orders'),
                'comment'       => Mage::helper('sales')->__('Total Ordered Report'),
                'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE),
            ],
            [
                'id'            => 'tax',
                'report'        => Mage::helper('sales')->__('Tax'),
                'comment'       => Mage::helper('sales')->__('Order Taxes Report Grouped by Tax Rates'),
                'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_TAX_FLAG_CODE),
            ],
            [
                'id'            => 'shipping',
                'report'        => Mage::helper('sales')->__('Shipping'),
                'comment'       => Mage::helper('sales')->__('Total Shipped Report'),
                'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_SHIPPING_FLAG_CODE),
            ],
            [
                'id'            => 'invoiced',
                'report'        => Mage::helper('sales')->__('Total Invoiced'),
                'comment'       => Mage::helper('sales')->__('Total Invoiced VS Paid Report'),
                'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_INVOICE_FLAG_CODE),
            ],
            [
                'id'            => 'refunded',
                'report'        => Mage::helper('sales')->__('Total Refunded'),
                'comment'       => Mage::helper('sales')->__('Total Refunded Report'),
                'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_REFUNDED_FLAG_CODE),
            ],
            [
                'id'            => 'coupons',
                'report'        => Mage::helper('sales')->__('Coupons'),
                'comment'       => Mage::helper('sales')->__('Promotion Coupons Usage Report'),
                'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_COUPONS_FLAG_CODE),
            ],
            [
                'id'            => 'bestsellers',
                'report'        => Mage::helper('sales')->__('Bestsellers'),
                'comment'       => Mage::helper('sales')->__('Products Bestsellers Report'),
                'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_BESTSELLERS_FLAG_CODE),
            ],
            [
                'id'            => 'viewed',
                'report'        => Mage::helper('sales')->__('Most Viewed'),
                'comment'       => Mage::helper('sales')->__('Most Viewed Products Report'),
                'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_PRODUCT_VIEWED_FLAG_CODE),
            ],
        ];

        foreach ($data as $value) {
            $item = new Varien_Object();
            $item->setData($value);
            $collection->addItem($item);
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('report', [
            'header'    => Mage::helper('reports')->__('Report'),
            'index'     => 'report',
            'type'      => 'string',
            'width'     => 150,
            'sortable'  => false,
        ]);

        $this->addColumn('comment', [
            'header'    => Mage::helper('reports')->__('Description'),
            'index'     => 'comment',
            'type'      => 'string',
            'sortable'  => false,
        ]);

        $this->addColumn('updated_at', [
            'header'    => Mage::helper('reports')->__('Updated At'),
            'index'     => 'updated_at',
            'type'      => 'datetime',
            'default'   => Mage::helper('reports')->__('undefined'),
            'sortable'  => false,
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('code');

        $this->getMassactionBlock()->addItem(MassAction::REFRESH_LIFETIME, [
            'label'    => Mage::helper('reports')->__('Refresh Lifetime Statistics'),
            'url'      => $this->getUrl('*/*/refreshLifetime'),
            'confirm'  => Mage::helper('reports')->__('Are you sure you want to refresh lifetime statistics? There can be performance impact during this operation.'),
        ]);

        $this->getMassactionBlock()->addItem(MassAction::REFRESH_RECENT, [
            'label'    => Mage::helper('reports')->__('Refresh Statistics for the Last Day'),
            'url'      => $this->getUrl('*/*/refreshRecent'),
            'confirm'  => Mage::helper('reports')->__('Are you sure?'),
            'selected' => true,
        ]);

        return $this;
    }
}
