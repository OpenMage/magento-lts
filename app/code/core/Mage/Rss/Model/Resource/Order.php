<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Rss
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order Rss Resource Model
 *
 * @category    Mage
 * @package     Mage_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Model_Resource_Order
{
    /**
     * Retrieve core resource model
     *
     * @return Mage_Core_Model_Resource
     */
    public function getCoreResource()
    {
        return Mage::getSingleton('core/resource');
    }

    /**
     * Retrieve order comments
     *
     * @param int $orderId
     * @return array
     */
    public function getAllCommentCollection($orderId)
    {
        $res = $this->getCoreResource();
        $read = $res->getConnection('core_read');

        $fields = [
            'notified' => 'is_customer_notified',
            'comment',
            'created_at',
        ];
        $commentSelects = [];
        foreach (['invoice', 'shipment', 'creditmemo'] as $entityTypeCode) {
            $mainTable  = $res->getTableName('sales/' . $entityTypeCode);
            $slaveTable = $res->getTableName('sales/' . $entityTypeCode . '_comment');
            $select = $read->select()
                ->from(['main' => $mainTable], [
                    'entity_id' => 'order_id',
                    'entity_type_code' => new Zend_Db_Expr("'$entityTypeCode'")
                ])
                ->join(['slave' => $slaveTable], 'main.entity_id = slave.parent_id', $fields)
                ->where('main.order_id = ?', $orderId);
            $commentSelects[] = '(' . $select . ')';
        }
        $select = $read->select()
            ->from($res->getTableName('sales/order_status_history'), [
                'entity_id' => 'parent_id',
                'entity_type_code' => new Zend_Db_Expr("'order'")
                ] + $fields)
            ->where('parent_id = ?', $orderId)
            ->where('is_visible_on_front > 0');
        $commentSelects[] = '(' . $select . ')';

        $commentSelect = $read->select()
            ->union($commentSelects, Zend_Db_Select::SQL_UNION_ALL);

        $select = $read->select()
            ->from(['orders' => $res->getTableName('sales/order')], ['increment_id'])
            ->join(['t' => $commentSelect],'t.entity_id = orders.entity_id')
            ->order('orders.created_at desc');

        return $read->fetchAll($select);
    }
}
