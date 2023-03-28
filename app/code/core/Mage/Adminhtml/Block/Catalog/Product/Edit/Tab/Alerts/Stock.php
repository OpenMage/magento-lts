<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sign up for an alert when the product price changes grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Alerts_Stock extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('alertStock');
        $this->setDefaultSort('add_date');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
        $this->setEmptyText(Mage::helper('catalog')->__('There are no customers for this alert.'));
    }

    protected function _prepareCollection()
    {
        $productId = $this->getRequest()->getParam('id');
        $websiteId = 0;
        if ($store = $this->getRequest()->getParam('store')) {
            $websiteId = Mage::app()->getStore($store)->getWebsiteId();
        }
        if (Mage::helper('catalog')->isModuleEnabled('Mage_ProductAlert')) {
            $collection = Mage::getModel('productalert/stock')
                ->getCustomerCollection()
                ->join($productId, $websiteId);
            $this->setCollection($collection);
        }
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('firstname', [
            'header'    => Mage::helper('catalog')->__('First Name'),
            'index'     => 'firstname',
        ]);

        $this->addColumn('middlename', [
            'header'    => Mage::helper('catalog')->__('Middle Name'),
            'index'     => 'middlename',
        ]);

        $this->addColumn('lastname', [
            'header'    => Mage::helper('catalog')->__('Last Name'),
            'index'     => 'lastname',
        ]);

        $this->addColumn('email', [
            'header'    => Mage::helper('catalog')->__('Email'),
            'index'     => 'email',
        ]);

        $this->addColumn('add_date', [
            'header'    => Mage::helper('catalog')->__('Date Subscribed'),
            'index'     => 'add_date',
            'type'      => 'date'
        ]);

        $this->addColumn('send_date', [
            'header'    => Mage::helper('catalog')->__('Last Notification'),
            'index'     => 'send_date',
            'type'      => 'date'
        ]);

        $this->addColumn('send_count', [
            'header'    => Mage::helper('catalog')->__('Send Count'),
            'index'     => 'send_count',
        ]);

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        $productId = $this->getRequest()->getParam('id');
        $storeId   = $this->getRequest()->getParam('store', 0);
        if ($storeId) {
            $storeId = Mage::app()->getStore($storeId)->getId();
        }
        return $this->getUrl('*/catalog_product/alertsStockGrid', [
            'id'    => $productId,
            'store' => $storeId
        ]);
    }
}
