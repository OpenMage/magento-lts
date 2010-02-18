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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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

    protected function _getUpdatedAt($reportCode)
    {
        $flag = Mage::getModel('reports/flag')->setReportFlagCode($reportCode)->loadSelf();
        return ($flag->hasData())
            ? Mage::app()->getLocale()->storeDate(
                0, new Zend_Date($flag->getLastUpdate(), Varien_Date::DATETIME_INTERNAL_FORMAT), true
            )
            : '';
    }

    protected function _prepareCollection()
    {
        $collection = new Varien_Data_Collection();

        $data = array(
            array(
                'id'            => 'sales',
                'report'        => Mage::helper('reports')->__('Sales'),
                'comment'       => Mage::helper('reports')->__('Total Ordered Report'),
                'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE)
            ),
            array(
                'id'            => 'tax',
                'report'        => Mage::helper('reports')->__('Tax'),
                'comment'       => Mage::helper('reports')->__('Order Taxes Report Grouped by Tax Rates'),
                'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_TAX_FLAG_CODE)
            ),
            array(
                'id'            => 'shipping',
                'report'        => Mage::helper('reports')->__('Shipping'),
                'comment'       => Mage::helper('reports')->__('Total Shipped Report'),
                'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_SHIPPING_FLAG_CODE)
            ),
            array(
                'id'            => 'invoiced',
                'report'        => Mage::helper('reports')->__('Total Invoiced'),
                'comment'       => Mage::helper('reports')->__('Total Invoiced VS Paid Report'),
                'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_INVOICE_FLAG_CODE)
            ),
            array(
                'id'            => 'refunded',
                'report'        => Mage::helper('reports')->__('Total Refunded'),
                'comment'       => Mage::helper('reports')->__('Total Refunded Report'),
                'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_REFUNDED_FLAG_CODE)
            ),
            array(
                'id'            => 'coupons',
                'report'        => Mage::helper('reports')->__('Coupons'),
                'comment'       => Mage::helper('reports')->__('Promotion Coupons Usage Report'),
                'updated_at'    => $this->_getUpdatedAt(Mage_Reports_Model_Flag::REPORT_COUPNS_FLAG_CODE)
            )
        );

        foreach ($data as $value) {
            $item = new Varien_Object();
            $item->setData($value);
            $collection->addItem($item);
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('report', array(
            'header'    => Mage::helper('reports')->__('Report'),
            'index'     => 'report',
            'type'      => 'string',
            'width'     => 150,
            'sortable'  => false
        ));

        $this->addColumn('comment', array(
            'header'    => Mage::helper('reports')->__('Description'),
            'index'     => 'comment',
            'type'      => 'string',
            'sortable'  => false
        ));

        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('reports')->__('Updated At'),
            'index'     => 'updated_at',
            'type'      => 'datetime',
            'width'     => 200,
            'sortable'  => false
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('code');

        $this->getMassactionBlock()->addItem('refresh_lifetime', array(
            'label'    => Mage::helper('reports')->__('Refresh Lifetime Statistics'),
            'url'      => $this->getUrl('*/*/refreshLifetime'),
            'confirm'  => Mage::helper('reports')->__('Are you sure you want to refresh lifetime statistics? There can be performance impact during this operation.')
        ));

        $this->getMassactionBlock()->addItem('refresh_recent', array(
            'label'    => Mage::helper('reports')->__('Refresh Statistics for Last Day'),
            'url'      => $this->getUrl('*/*/refreshRecent'),
            'confirm'  => Mage::helper('reports')->__('Are you sure?'),
            'selected' => true
        ));

        return $this;
    }
}
