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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Flat sales order invoice resource
 *
 */
class Mage_Sales_Model_Mysql4_Order_Invoice extends Mage_Sales_Model_Mysql4_Order_Abstract
{
    protected $_eventPrefix = 'sales_order_invoice_resource';
    protected $_grid = true;
    protected $_useIncrementId = true;
    protected $_entityTypeForIncrementId = 'invoice';

    protected function _construct()
    {
        $this->_init('sales/invoice', 'entity_id');
    }

    /**
     * Init virtual grid records for entity
     *
     * @return Mage_Sales_Model_Mysql4_Order_Invoice
     */
    protected function _initVirtualGridColumns()
    {
        parent::_initVirtualGridColumns();
        $this->addVirtualGridColumn(
                'billing_name',
                'sales/order_address',
                array('billing_address_id' => 'entity_id'),
                'CONCAT(IFNULL({{table}}.firstname, ""), " ", IFNULL({{table}}.lastname, ""))'
            )
            ->addVirtualGridColumn(
                'order_increment_id',
                'sales/order',
                array('order_id' => 'entity_id'),
                'increment_id'
            )
            ->addVirtualGridColumn(
                'order_created_at',
                'sales/order',
                array('order_id' => 'entity_id'),
                'created_at'
            )
            ;

        return $this;
    }
}
