<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Log
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Log Prepare Online visitors resource
 *
 * @category   Mage
 * @package    Mage_Log
 */
class Mage_Log_Model_Resource_Visitor_Online extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('log/visitor_online', 'visitor_id');
    }

    /**
     * Prepare online visitors for collection
     *
     * @return $this
     */
    public function prepare(Mage_Log_Model_Visitor_Online $object)
    {
        if (($object->getUpdateFrequency() + $object->getPrepareAt()) > time()) {
            return $this;
        }

        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();

        $writeAdapter->beginTransaction();

        try {
            $writeAdapter->delete($this->getMainTable());

            $visitors = [];
            $lastUrls = [];

            // retrieve online visitors general data

            $lastDate = Mage::getModel('core/date')->gmtTimestamp() - $object->getOnlineInterval() * 60;

            $select = $readAdapter->select()
                ->from(
                    $this->getTable('log/visitor'),
                    ['visitor_id', 'first_visit_at', 'last_visit_at', 'last_url_id'],
                )
                ->where('last_visit_at >= ?', $readAdapter->formatDate($lastDate));

            $query = $readAdapter->query($select);
            while ($row = $query->fetch()) {
                $visitors[$row['visitor_id']] = $row;
                $lastUrls[$row['last_url_id']] = $row['visitor_id'];
                $visitors[$row['visitor_id']]['visitor_type'] = Mage_Log_Model_Visitor::VISITOR_TYPE_VISITOR;
                $visitors[$row['visitor_id']]['customer_id']  = null;
            }

            if (!$visitors) {
                $this->commit();
                return $this;
            }

            // retrieve visitor remote addr
            $select = $readAdapter->select()
                ->from(
                    $this->getTable('log/visitor_info'),
                    ['visitor_id', 'remote_addr'],
                )
                ->where('visitor_id IN(?)', array_keys($visitors));

            $query = $readAdapter->query($select);
            while ($row = $query->fetch()) {
                $visitors[$row['visitor_id']]['remote_addr'] = $row['remote_addr'];
            }

            // retrieve visitor last URLs
            $select = $readAdapter->select()
                ->from(
                    $this->getTable('log/url_info_table'),
                    ['url_id', 'url'],
                )
                ->where('url_id IN(?)', array_keys($lastUrls));

            $query = $readAdapter->query($select);
            while ($row = $query->fetch()) {
                $visitorId = $lastUrls[$row['url_id']];
                $visitors[$visitorId]['last_url'] = $row['url'];
            }

            // retrieve customers
            $select = $readAdapter->select()
                ->from(
                    $this->getTable('log/customer'),
                    ['visitor_id', 'customer_id'],
                )
                ->where('visitor_id IN(?)', array_keys($visitors));

            $query = $readAdapter->query($select);
            while ($row = $query->fetch()) {
                $visitors[$row['visitor_id']]['visitor_type'] = Mage_Log_Model_Visitor::VISITOR_TYPE_CUSTOMER;
                $visitors[$row['visitor_id']]['customer_id']  = $row['customer_id'];
            }

            foreach ($visitors as $visitorData) {
                unset($visitorData['last_url_id']);

                $writeAdapter->insertForce($this->getMainTable(), $visitorData);
            }

            $writeAdapter->commit();
        } catch (Exception $e) {
            $writeAdapter->rollBack();
            throw $e;
        }

        $object->setPrepareAt();

        return $this;
    }
}
