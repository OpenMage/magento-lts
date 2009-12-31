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
 * @package     Mage_Oscommerce
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * osCommerce convert grid block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Oscommerce_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('oscommerceOrderGrid');
        $this->setDefaultSort('id');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('oscommerce/oscommerce_order')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('order_id', array(
            'header'    =>Mage::helper('oscommerce')->__('Order #'),
            'width'     =>'50px',
            'index'     =>'osc_magento_id',
            'type'      => 'number',
            ));

        $this->addColumn('billing_name', array(
            'header'    =>Mage::helper('oscommerce')->__('Billing to Name'),
            'index'     =>'billing_name',
        ));

        $this->addColumn('delivery_name', array(
            'header'    =>Mage::helper('oscommerce')->__('Ship to Name'),
            'index'     =>'delivery_name',
        ));

        $this->addColumn('currency', array(
            'header' =>Mage::helper('oscommerce')->__('Currency'),
            'width' =>'50px',
            'index' =>'currency',
        ));

        $this->addColumn('orders_total', array(
            'header' =>Mage::helper('oscommerce')->__('Order Total'),
            'width' =>'50px',
            'index' =>'orders_total',
            'type' => 'currency',
            'currency'=>'order_currency_code'
        ));

        $this->addColumn('orders_status', array(
            'header' =>Mage::helper('oscommerce')->__('Order Status'),
            'width' =>'50px',
            'index' =>'orders_status',
        ));

        $this->addColumn('date_purchased', array(
            'header'    => Mage::helper('oscommerce')->__('Purchased Year'),
            'width' 	=> '150px',
            'index'     => 'date_purchased',
            'type'		=> 'datetime',
        ));

//        $this->addColumn('purchased_year', array(
//            'header'    =>Mage::helper('oscommerce')->__('Purchased Year'),
//            'width' =>'50px',
//            'index'     =>'purchased_year',
//            'type'	=> 'currency',
//            'currency' => 'store_currency_code',
//        ));
//
//        $this->addColumn('purchased_month', array(
//            'header'    =>Mage::helper('oscommerce')->__('Purchased Month'),
//            'width' =>'50px',
//            'index'     =>'purchased_month',
//            'type'	=> 'currency',
//            'currency' => 'store_currency_code',
//        ));
//
//        $this->addColumn('purchased_day', array(
//            'header'    =>Mage::helper('oscommerce')->__('Purchased Date'),
//            'width' =>'50px',
//            'index'     =>'purchased_day',
//            'type'	=> 'currency',
//            'currency' => 'store_currency_code',
//        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('order_id'=>$row->getId()));
    }

}
