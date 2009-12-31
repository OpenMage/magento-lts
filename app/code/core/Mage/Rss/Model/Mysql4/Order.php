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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Order Rss
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Model_Mysql4_Order
{
    protected $_entityTypeIdsToTypes;
    protected $_entityIdsToIncrementIds;

    public function __construct()
    {
        $this->_entityTypeIdsToTypes = array();
        $this->_entityIdsToIncrementIds = array();
    }

    public function getEntityTypeIdsToTypes()
    {
        return $this->_entityTypeIdsToTypes;
    }

    public function getEntityIdsToIncrementIds()
    {
        return $this->_entityIdsToIncrementIds;
    }

    public function getCoreResource()
    {
        return Mage::getSingleton('core/resource');
    }

    public function getAllOrderEntityTypeIds()
    {
        $orderEntityTypes = array();
        $etypeIds = array();
        $oattrIds = array();
        $eav = Mage::getSingleton('eav/config');
        $oTable = '';
        foreach (array(
                'invoice'=>'sales/order_invoice',
                'shipment'=>'sales/order_shipment',
                'creditmemo'=>'sales/order_creditmemo'
            ) as $entityTypeCode=>$entityModel) {
                $entityType = $eav->getEntityType($entityTypeCode);
                $entity = Mage::getResourceSingleton($entityModel);
                $orderAttr = $eav->getAttribute($entityType, 'order_id');
                if (!$oTable) {
                    $orderAttr->setEntity($entity);
                    $oTable = $orderAttr->getBackend()->getTable();
                }
                $this->_entityTypeIdsToTypes[$entityType->getId()] = $entityTypeCode;
                $etypeIds[$entityType->getId()] = $entityTypeCode;
                $oattrIds[] = $orderAttr->getId();
        }
        $orderEntityTypes = array(
            'entityTypeIds' => $etypeIds,
            'orderAttrIds' => $oattrIds,
            'order_table' => $oTable);
        return $orderEntityTypes;
    }

    public function getAllOrderEntityIds($oid, $orderEntityTypes)
    {
        $etypeIdsArr = array_keys($orderEntityTypes['entityTypeIds']);
        $res = $this->getCoreResource();
        $read = $res->getConnection('core_read');
        $select = $read->select()
             ->from(array('order' => $res->getTableName('sales/order')), array('entity_id'))
             ->join($orderEntityTypes['order_table'],"{$orderEntityTypes['order_table']}.entity_id=order.entity_id
             and {$orderEntityTypes['order_table']}.attribute_id in (".implode(',',$orderEntityTypes['orderAttrIds']).")
             and {$orderEntityTypes['order_table']}.entity_type_id in (".implode(',', $etypeIdsArr).") and {$orderEntityTypes['order_table']}.value={$oid}"
             ,array("{$orderEntityTypes['order_table']}.value"));

        $results = $read->fetchAll($select);
        $eIds = array($oid);
        foreach($results as $result){
            $eIds[] = $result['entity_id'];
        }
        return $eIds;
    }

    public function getAllEntityIds($entityIds = array())
    {
        $res = $this->getCoreResource();
        $read = $res->getConnection('core_read');
        $entityIdStr = implode(',', $entityIds);
        $select = $read->select()
             ->from($res->getTableName('sales/order'), array('entity_id','increment_id'))
             ->where('parent_id in (' .$entityIdStr.')')
             ->orWhere('entity_id in (' .$entityIdStr.')');
        $results = $read->fetchAll($select);
        $eIds = array();
        foreach($results as $result){
            if($result['increment_id']) {
               $this->_entityIdsToIncrementIds[$result['entity_id']] = $result['increment_id'];
            }
            $eIds[] = $result['entity_id'];
        }
        return $eIds;
    }

    public function getAllEntityTypeCommentIds()
    {
        $entityTypes = array();
        $eav = Mage::getSingleton('eav/config');
        $etypeIds = array();
        $cattrIds = array();
        $nattrIds = array();
        $cTable = '';
        $nTable = '';
        foreach (array(
                'order_status_history'=>array('model' => 'sales/order_status_history', 'type' => 'order'),
                'invoice_comment'=>array('model' => 'sales/order_invoice_comment', 'type' => 'invoice'),
                'shipment_comment'=>array('model' => 'sales/order_shipment_comment', 'type' => 'shipment'),
                'creditmemo_comment'=>array('model' => 'sales/order_creditmemo_comment', 'type' => 'creditmemo')
            ) as $entityTypeCode=>$entityArr) {

            $entityType = $eav->getEntityType($entityTypeCode);
            $entity = Mage::getResourceSingleton($entityArr['model']);
            $commentAttr = $eav->getAttribute($entityType, 'comment');
            $notifiedAttr = $eav->getAttribute($entityType, 'is_customer_notified');
            $statusAttr = $eav->getAttribute($entityType, 'status');
#$statusAttr->setEntity($entity);
#echo "****".$statusAttr->getBackend()->getTable()."****".$statusAttr->getId();
            if (!$cTable) {
                $commentAttr->setEntity($entity);
                $cTable = $commentAttr->getBackend()->getTable();
            }
            if (!$nTable) {
                $notifiedAttr->setEntity($entity);
                $nTable = $notifiedAttr->getBackend()->getTable();
            }
            $etypeIds[] = $entityType->getId();
            $cattrIds[] = $commentAttr->getId();
            $nattrIds[] = $notifiedAttr->getId();
            $this->_entityTypeIdsToTypes[$entityType->getId()] = $entityArr['type'];
            /*
            $entityTypes[$entityType->getId()] = array(
                'table'=>$entityType->getEntityTable(),
                'alias'  => $entityTypeCode,
                'comment_attribute_id'=>$commentAttr->getId(),
                'notified_attribute_id'=>$notifiedAttr->getId(),
            );
            */
        }
        $entityTypes = array(
            'entityTypeIds' => $etypeIds,
            'commentAttrIds' => $cattrIds,
            'notifiedAttrIds' => $nattrIds,
            'comment_table' => $cTable,
            'notified_table' => $nTable);
        return $entityTypes;
    }

    /*
    entity_type_id IN (order_status_history, invoice_comment, shipment_comment, creditmemo_comment)
    entity_id IN(order_id, credimemo_ids, invoice_ids, shipment_ids)
    attribute_id IN(order_status/comment_text, ....)
    */
    public function getAllCommentCollection($oid)
    {
        $orderEntityTypes = $this->getAllOrderEntityTypeIds();
        $entityIds = $this->getAllOrderEntityIds($oid, $orderEntityTypes);
        $allEntityIds = $this->getAllEntityIds($entityIds);

        $eTypes = $this->getAllEntityTypeCommentIds();
        $etypeIds = implode(',',$eTypes['entityTypeIds']);

        /*foreach($entityTypeIds as $eid=>$result){
            $etIds[] = $eid;
            $attributeIds[] = $result['comment_attribute_id'];
            $attributeIds[] = $result['notified_attribute_id'];
        }*/

        $res = $this->getCoreResource();
        $read = $res->getConnection('core_read');
        $select = $read->select()
             ->from(array('order' => $res->getTableName('sales/order')), array('entity_id','created_at','entity_type_id','parent_id'))
             ->where('order.entity_id in ('.implode(",", $allEntityIds).')')
             ->join($eTypes['comment_table'],"{$eTypes['comment_table']}.entity_id=order.entity_id
             and {$eTypes['comment_table']}.attribute_id in (".implode(',',$eTypes['commentAttrIds']).")
             and {$eTypes['comment_table']}.entity_type_id in (".$etypeIds.")"
             ,array('comment' => "{$eTypes['comment_table']}.value"))
             ->join($eTypes['notified_table'],"{$eTypes['notified_table']}.entity_id=order.entity_id
             and {$eTypes['notified_table']}.attribute_id in (".implode(',',$eTypes['notifiedAttrIds']).")
             and {$eTypes['notified_table']}.entity_type_id in (".$etypeIds.") and {$eTypes['notified_table']}.value=1"
             ,array('notified' =>"{$eTypes['notified_table']}.value"))
             ->order('created_at desc')
        ;
        return $read->fetchAll($select);

    }

}
