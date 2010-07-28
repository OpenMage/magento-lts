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
 * Flat sales order resource
 *
 */
class Mage_Sales_Model_Mysql4_Order extends Mage_Sales_Model_Mysql4_Order_Abstract
{
    protected $_eventPrefix = 'sales_order_resource';
    protected $_eventObject = 'resource';
    protected $_grid = true;
    protected $_useIncrementId = true;
    protected $_entityCodeForIncrementId = 'order';

    protected function _construct()
    {
        $this->_init('sales/order', 'entity_id');
    }

    /**
     * Init virtual grid records for entity
     *
     * @return Mage_Sales_Model_Mysql4_Order
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
                'shipping_name',
                'sales/order_address',
                 array('shipping_address_id' => 'entity_id'),
                 'CONCAT(IFNULL({{table}}.firstname, ""), " ", IFNULL({{table}}.lastname, ""))'
            );

        return $this;
    }

    /**
     * Count existent products of order items by specified product types
     *
     * @param int $orderId
     * @param array $productTypeIds
     * @param bool $isProductTypeIn
     * @return array
     */
    public function aggregateProductsByTypes($orderId, $productTypeIds = array(), $isProductTypeIn = false)
    {
        $select = $this->getReadConnection()->select()
            ->from(array('o' => $this->getTable('sales/order_item')), new Zend_Db_Expr('o.product_type, COUNT(*)'))
            ->joinInner(array('p' => $this->getTable('catalog/product')), 'o.product_id=p.entity_id', array())
            ->where('o.order_id=?', $orderId)
            ->group('(1)')
        ;
        if ($productTypeIds) {
            $select->where($this->getReadConnection()->quoteInto(
                sprintf('(o.product_type %s (?))', ($isProductTypeIn ? 'IN' : 'NOT IN')),
                $productTypeIds
            ));
        }
        return $this->getReadConnection()->fetchPairs($select);
    }

    /**
     * Retrieve order_increment_id by order_id
     *
     * @param int $orderId
     * @return string
     */
    public function getIncrementId($orderId)
    {
        $select = $this->getReadConnection()->select()
            ->from($this->getMainTable(), array("increment_id"))
            ->where('entity_id = ?', $orderId);
        return $this->getReadConnection()->fetchOne($select);
    }
}
