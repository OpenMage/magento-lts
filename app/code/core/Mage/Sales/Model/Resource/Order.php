<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Flat sales order resource
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Resource_Order extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix                  = 'sales_order_resource';

    /**
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
     * @var bool
     */
    protected $_useIncrementId               = true;

    /**
     * Entity code for increment id
     *
     * @var string
     */
    protected $_entityCodeForIncrementId     = 'order';

    protected function _construct()
    {
        $this->_init('sales/order', 'entity_id');
    }

    /**
     * Init virtual grid records for entity
     *
     * @return $this
     */
    protected function _initVirtualGridColumns()
    {
        parent::_initVirtualGridColumns();
        $adapter       = $this->getReadConnection();
        $ifnullFirst   = $adapter->getIfNullSql('{{table}}.firstname', $adapter->quote(''));
        $ifnullMiddle  = $adapter->getIfNullSql('{{table}}.middlename', $adapter->quote(''));
        $ifnullLast    = $adapter->getIfNullSql('{{table}}.lastname', $adapter->quote(''));
        $concatAddress = $adapter->getConcatSql([
            $ifnullFirst,
            $adapter->quote(' '),
            $ifnullMiddle,
            $adapter->quote(' '),
            $ifnullLast
        ]);
        $concatAddress = new Zend_Db_Expr("TRIM(REPLACE($concatAddress,'  ', ' '))");

        $this->addVirtualGridColumn(
            'billing_name',
            'sales/order_address',
            ['billing_address_id' => 'entity_id'],
            $concatAddress
        )
            ->addVirtualGridColumn(
                'shipping_name',
                'sales/order_address',
                ['shipping_address_id' => 'entity_id'],
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
    public function aggregateProductsByTypes($orderId, $productTypeIds = [], $isProductTypeIn = false)
    {
        $adapter = $this->getReadConnection();
        $select  = $adapter->select()
            ->from(
                ['o' => $this->getTable('sales/order_item')],
                ['o.product_type', new Zend_Db_Expr('COUNT(*)')]
            )
            ->joinInner(
                ['p' => $this->getTable('catalog/product')],
                'o.product_id=p.entity_id',
                []
            )
            ->where('o.order_id=?', $orderId)
            ->group('o.product_type')
        ;
        if ($productTypeIds) {
            $select->where(
                sprintf('(o.product_type %s (?))', ($isProductTypeIn ? 'IN' : 'NOT IN')),
                $productTypeIds
            );
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
        $bind    = [':entity_id' => $orderId];
        $select  = $adapter->select()
            ->from($this->getMainTable(), ['increment_id'])
            ->where('entity_id = :entity_id');
        return $adapter->fetchOne($select, $bind);
    }
}
