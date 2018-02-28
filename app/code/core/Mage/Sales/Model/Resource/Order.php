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
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Flat sales order resource
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Order extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix                  = 'sales_order_resource';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject                  = 'resource';

    /**
     * Is grid
     *
     * @var boolean
     */
    protected $_grid                         = true;

    /**
     * Use increment id
     *
     * @var boolean
     */
    protected $_useIncrementId               = true;

    /**
     * Entity code for increment id
     *
     * @var string
     */
    protected $_entityCodeForIncrementId     = 'order';

    /**
     * Model Initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales/order', 'entity_id');
    }

    /**
     * Init virtual grid records for entity
     *
     * @return Mage_Sales_Model_Resource_Order
     */
    protected function _initVirtualGridColumns()
    {
        parent::_initVirtualGridColumns();
        $adapter       = $this->getReadConnection();
        $ifnullFirst   = $adapter->getIfNullSql('{{table}}.firstname', $adapter->quote(''));
        $ifnullMiddle  = $adapter->getIfNullSql('{{table}}.middlename', $adapter->quote(''));
        $ifnullLast    = $adapter->getIfNullSql('{{table}}.lastname', $adapter->quote(''));
        $concatAddress = $adapter->getConcatSql(array(
            $ifnullFirst,
            $adapter->quote(' '),
            $ifnullMiddle,
            $adapter->quote(' '),
            $ifnullLast
        ));
        $this->addVirtualGridColumn(
                'billing_name',
                'sales/order_address',
                array('billing_address_id' => 'entity_id'),
                $concatAddress
            )
            ->addVirtualGridColumn(
                'shipping_name',
                'sales/order_address',
                 array('shipping_address_id' => 'entity_id'),
                 $concatAddress
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
        $adapter = $this->getReadConnection();
        $select  = $adapter->select()
            ->from(
                array('o' => $this->getTable('sales/order_item')),
                array('o.product_type', new Zend_Db_Expr('COUNT(*)')))
            ->joinInner(
                array('p' => $this->getTable('catalog/product')),
                'o.product_id=p.entity_id',
                array())
            ->where('o.order_id=?', $orderId)
            ->group('o.product_type')
        ;
        if ($productTypeIds) {
            $select->where(
                sprintf('(o.product_type %s (?))', ($isProductTypeIn ? 'IN' : 'NOT IN')),
                $productTypeIds);
        }
        return $adapter->fetchPairs($select);
    }

    /**
     * Retrieve order_increment_id by order_id
     *
     * @param int $orderId
     * @return string
     */
    public function getIncrementId($orderId)
    {
        $adapter = $this->getReadConnection();
        $bind    = array(':entity_id' => $orderId);
        $select  = $adapter->select()
            ->from($this->getMainTable(), array("increment_id"))
            ->where('entity_id = :entity_id');
        return $adapter->fetchOne($select, $bind);
    }
}
