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
 * @package     Mage_Reports
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Products Report collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Reports_Model_Mysql4_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
    const SELECT_COUNT_SQL_TYPE_CART    = 1;

    protected $_productEntityId;
    protected $_productEntityTableName;
    protected $_productEntityTypeId;
    protected $_selectCountSqlType = 0;

    /**
     * Set Type for COUNT SQL Select
     *
     * @param int $type
     * @return Mage_Reports_Model_Mysql4_Product_Collection
     */
    public function setSelectCountSqlType($type)
    {
        $this->_selectCountSqlType = $type;
        return $this;
    }

    public function setProductEntityId($value)
    {
        $this->_productEntityId = $value;
        return $this;
    }

    public function getProductEntityId()
    {
        return $this->_productEntityId;
    }

    public function setProductEntityTableName($value)
    {
        $this->_productEntityTableName = $value;
        return $this;
    }

    public function getProductEntityTableName()
    {
        return $this->_productEntityTableName;
    }

    public function setProductEntityTypeId($value)
    {
        $this->_productEntityTypeId = $value;
        return $this;
    }

    public function getProductEntityTypeId()
    {
        return $this->_productEntityTypeId;
    }

    public function __construct()
    {
        $product = Mage::getResourceSingleton('catalog/product');
        /* @var $product Mage_Catalog_Model_Entity_Product */
        $this->setProductEntityId($product->getEntityIdField());
        $this->setProductEntityTableName($product->getEntityTable());
        $this->setProductEntityTypeId($product->getTypeId());

        parent::__construct();
    }

    protected function _joinFields()
    {
        $this->_totals = new Varien_Object();

        $this->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price');
        /*$this->getSelect()->columns(array(
                    'viewed' => 'CONCAT("","")',
                    'added' => 'CONCAT("","")',
                    'purchased' => 'CONCAT("","")',
                    'fulfilled' => 'CONCAT("","")',
                    'revenue' => 'CONCAT("","")',
                   ));*/
    }

    public function getSelectCountSql()
    {
        if ($this->_selectCountSqlType == self::SELECT_COUNT_SQL_TYPE_CART) {
            $countSelect = clone $this->getSelect();
            $countSelect->reset()
                ->from(array('quote_item_table' => $this->getTable('sales/quote_item')), 'COUNT(DISTINCT quote_item_table.product_id)')
                ->join(
                    array('quote_table' => $this->getTable('sales/quote')),
                    'quote_table.entity_id = quote_item_table.quote_id AND quote_table.is_active = 1',
                    array()
                );
            return $countSelect->__toString();
        }

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);
        $countSelect->columns("count(DISTINCT e.entity_id)");
        $sql = $countSelect->__toString();
        return $sql;
    }

    public function addCartsCount()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset();

        $countSelect->from(array("quote_items" => $this->getTable('sales/quote_item')), "count(*)")
            ->join(array('quotes' => $this->getTable('sales/quote')),
                'quotes.entity_id = quote_items.quote_id AND quotes.is_active = 1',
                array())
            ->where("quote_items.product_id = e.entity_id");

        $this->getSelect()
            ->columns(array("carts" => "({$countSelect})"))
            ->group("e.{$this->getProductEntityId()}")
            ->having('carts > 0');

        return $this;
    }

    public function addOrdersCount($from = '', $to = '')
    {
        $this->getSelect()
            ->joinLeft(array("order_items" => $this->getTable('sales/order_item')),
                "order_items.product_id = e.{$this->getProductEntityId()}", array())
            ->columns(array("orders" => "count(`order_items2`.item_id)"))
            ->group("e.{$this->getProductEntityId()}");

        if ($from != '' && $to != '') {
            $dateFilter = " and order_items2.created_at BETWEEN '{$from}' AND '{$to}'";
        } else {
            $dateFilter = '';
        }

        $this->getSelect()
            ->joinLeft(array("order_items2" => $this->getTable('sales/order_item')),
                "order_items2.item_id = order_items.item_id".$dateFilter, array());

        return $this;
    }

    public function addOrderedQty($from = '', $to = '')
    {
        $qtyOrderedTableName = $this->getTable('sales/order_item');
        $qtyOrderedFieldName = 'qty_ordered';

        $productIdTableName = $this->getTable('sales/order_item');
        $productIdFieldName = 'product_id';

        $compositeTypeIds = Mage::getSingleton('catalog/product_type')->getCompositeTypes();
        $productTypes = $this->getConnection()->quoteInto(' AND (e.type_id NOT IN (?))', $compositeTypeIds);

        if ($from != '' && $to != '') {
            $dateFilter = " AND `order`.created_at BETWEEN '{$from}' AND '{$to}'";
        } else {
            $dateFilter = "";
        }

        $this->getSelect()->reset()->from(
            array('order_items' => $qtyOrderedTableName),
            array('ordered_qty' => "SUM(order_items.{$qtyOrderedFieldName})")
        );

        $order = Mage::getResourceSingleton('sales/order');
        /* @var $order Mage_Sales_Model_Entity_Order */
        $stateAttr = $order->getAttribute('state');
        if ($stateAttr->getBackend()->isStatic()) {

            $_joinCondition = $this->getConnection()->quoteInto(
                'order.entity_id = order_items.order_id AND order.state<>?', Mage_Sales_Model_Order::STATE_CANCELED
            );
            $_joinCondition .= $dateFilter;

            $this->getSelect()->joinInner(
                array('order' => $this->getTable('sales/order')),
                $_joinCondition,
                array()
            );
        } else {

            $_joinCondition = 'order.entity_id = order_state.entity_id';
            $_joinCondition .= $this->getConnection()->quoteInto(' AND order_state.attribute_id=? ', $stateAttr->getId());
            $_joinCondition .= $this->getConnection()->quoteInto(' AND order_state.value<>? ', Mage_Sales_Model_Order::STATE_CANCELED);

            $this->getSelect()
                ->joinInner(
                    array('order' => $this->getTable('sales/order')),
                    'order.entity_id = order_items.order_id' . $dateFilter,
                    array())
                ->joinInner(
                    array('order_state' => $stateAttr->getBackend()->getTable()),
                    $_joinCondition,
                    array());
        }

        $this->getSelect()
            ->joinInner(array('e' => $this->getProductEntityTableName()),
                "e.entity_id = order_items.{$productIdFieldName} AND e.entity_type_id = {$this->getProductEntityTypeId()}{$productTypes}")
            ->group('e.entity_id')
            ->having('ordered_qty > 0');

        return $this;
    }

    public function setOrder($attribute, $dir='desc')
    {
        switch ($attribute)
        {
            case 'carts':
            case 'orders':
            case 'ordered_qty':
                $this->getSelect()->order($attribute . ' ' . $dir);
                break;
            default:
                parent::setOrder($attribute, $dir);
        }

        return $this;
    }

    public function addViewsCount($from = '', $to = '')
    {
        /**
         * Getting event type id for catalog_product_view event
         */
        foreach (Mage::getModel('reports/event_type')->getCollection() as $eventType) {
            if ($eventType->getEventName() == 'catalog_product_view') {
                $productViewEvent = $eventType->getId();
                break;
            }
        }

        $this->getSelect()->reset()
            ->from(
                array('_table_views' => $this->getTable('reports/event')),
                array('views' => 'COUNT(_table_views.event_id)'))
            ->join(array('e' => $this->getProductEntityTableName()),
                "e.entity_id = _table_views.object_id AND e.entity_type_id = {$this->getProductEntityTypeId()}")
            ->where('_table_views.event_type_id = ?', $productViewEvent)
            ->group('e.entity_id')
            ->order('views desc')
            ->having('views > 0');

        if ($from != '' && $to != '') {
            $this->getSelect()
                ->where('logged_at >= ?', $from)
                ->where('logged_at <= ?', $to);
        }

        return $this;
    }
}
