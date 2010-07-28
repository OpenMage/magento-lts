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
 * @package     Mage_Rss
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order Rss Resource Model
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Model_Mysql4_Order
{
    /**
     * @var array
     * @deprecated after 1.4.1.0
     */
    protected $_entityTypeIdsToTypes = array();

    /**
     * @var array
     * @deprecated after 1.4.1.0
     */
    protected $_entityIdsToIncrementIds = array();

    /**
     * @return array
     * @deprecated after 1.4.1.0
     */
    public function getEntityTypeIdsToTypes()
    {
        return $this->_entityTypeIdsToTypes;
    }

    /**
     * @return array
     * @deprecated after 1.4.1.0
     */
    public function getEntityIdsToIncrementIds()
    {
        return $this->_entityIdsToIncrementIds;
    }

    /**
     * @return array
     * @deprecated after 1.4.1.0
     */
    public function getAllOrderEntityTypeIds()
    {
        return array();
    }

    /**
     * @return array
     * @deprecated after 1.4.1.0
     */
    public function getAllOrderEntityIds($orderId, $orderEntityTypes)
    {
        return array();
    }

    /**
     * @return array
     * @deprecated after 1.4.1.0
     */
    public function getAllEntityIds($entityIds = array())
    {
        return array();
    }

    /**
     * @return array
     * @deprecated after 1.4.1.0
     */
    public function getAllEntityTypeCommentIds()
    {
        return array();
    }

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

        $fields = array(
            'notified' => 'is_customer_notified',
            'comment',
            'created_at',
        );
        $commentSelects = array();
        foreach (array('invoice', 'shipment', 'creditmemo') as $entityTypeCode) {
            $mainTable  = $res->getTableName('sales/' . $entityTypeCode);
            $slaveTable = $res->getTableName('sales/' . $entityTypeCode . '_comment');
            $select = $read->select()
                ->from(array('main' => $mainTable), array(
                    'entity_id' => 'order_id',
                    'entity_type_code' => new Zend_Db_Expr("'$entityTypeCode'")
                ))
                ->join(array('slave' => $slaveTable), 'main.entity_id = slave.parent_id', $fields)
                ->where('main.order_id = ?', $orderId);
            $commentSelects[] = '(' . $select . ')';
        }
        $select = $read->select()
            ->from($res->getTableName('sales/order_status_history'), array(
                'entity_id' => 'parent_id',
                'entity_type_code' => new Zend_Db_Expr("'order'")
            ) + $fields)
            ->where('parent_id = ?', $orderId)
            ->where('is_visible_on_front > 0');
        $commentSelects[] = '(' . $select . ')';

        $commentSelect = $read->select()
            ->union($commentSelects)
            ->order('created_at desc');

        $select = $read->select()
            ->from(array('order' => $res->getTableName('sales/order')), array('increment_id'))
            ->join(array('t' => $commentSelect), 't.entity_id = order.entity_id');

        return $read->fetchAll($select);
    }

}
