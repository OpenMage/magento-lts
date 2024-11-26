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
 * Log Resource Model
 *
 * @category   Mage
 * @package    Mage_Log
 */
class Mage_Log_Model_Resource_Log extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('log/visitor', 'visitor_id');
    }

    /**
     * Clean logs
     *
     * @return $this
     */
    public function clean(Mage_Log_Model_Log $object)
    {
        $cleanTime = $object->getLogCleanTime();

        Mage::dispatchEvent('log_log_clean_before', [
            'log'   => $object
        ]);

        $this->_cleanVisitors($cleanTime);
        $this->_cleanCustomers($cleanTime);
        $this->_cleanUrls();

        Mage::dispatchEvent('log_log_clean_after', [
            'log'   => $object
        ]);

        return $this;
    }

    /**
     * Clean visitors table
     *
     * @param int $time
     * @return $this
     */
    protected function _cleanVisitors($time)
    {
        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();

        $timeLimit = $this->formatDate(Mage::getModel('core/date')->gmtTimestamp() - $time);

        while (true) {
            $select = $readAdapter->select()
                ->from(
                    ['visitor_table' => $this->getTable('log/visitor')],
                    ['visitor_id' => 'visitor_table.visitor_id']
                )
                ->joinLeft(
                    ['customer_table' => $this->getTable('log/customer')],
                    'visitor_table.visitor_id = customer_table.visitor_id AND customer_table.log_id IS NULL',
                    []
                )
                ->where('visitor_table.last_visit_at < ?', $timeLimit)
                ->limit(100);

            $visitorIds = $readAdapter->fetchCol($select);

            if (!$visitorIds) {
                break;
            }

            $condition = ['visitor_id IN (?)' => $visitorIds];

            // remove visitors from log/quote
            $writeAdapter->delete($this->getTable('log/quote_table'), $condition);

            // remove visitors from log/url
            $writeAdapter->delete($this->getTable('log/url_table'), $condition);

            // remove visitors from log/visitor_info
            $writeAdapter->delete($this->getTable('log/visitor_info'), $condition);

            // remove visitors from log/visitor
            $writeAdapter->delete($this->getTable('log/visitor'), $condition);
        }

        return $this;
    }

    /**
     * Clean customer table
     *
     * @param int $time
     * @return $this
     */
    protected function _cleanCustomers($time)
    {
        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();

        $timeLimit = $this->formatDate(Mage::getModel('core/date')->gmtTimestamp() - $time);

        // retrieve last active customer log id
        $lastLogId = $readAdapter->fetchOne(
            $readAdapter->select()
                ->from($this->getTable('log/customer'), 'log_id')
                ->where('login_at < ?', $timeLimit)
                ->order('log_id DESC')
                ->limit(1)
        );

        if (!$lastLogId) {
            return $this;
        }

        // Order by desc log_id before grouping (within-group aggregates query pattern)
        $select = $readAdapter->select()
            ->from(
                ['log_customer_main' => $this->getTable('log/customer')],
                ['log_id']
            )
            ->joinLeft(
                ['log_customer' => $this->getTable('log/customer')],
                'log_customer_main.customer_id = log_customer.customer_id '
                    . 'AND log_customer_main.log_id < log_customer.log_id',
                []
            )
            ->where('log_customer.customer_id IS NULL')
            ->where('log_customer_main.log_id < ?', $lastLogId + 1);

        $needLogIds = [];
        $query = $readAdapter->query($select);
        while ($row = $query->fetch()) {
            $needLogIds[$row['log_id']] = 1;
        }

        $customerLogId = 0;
        while (true) {
            $visitorIds = [];
            $select = $readAdapter->select()
                ->from(
                    $this->getTable('log/customer'),
                    ['log_id', 'visitor_id']
                )
                ->where('log_id > ?', $customerLogId)
                ->where('log_id < ?', $lastLogId + 1)
                ->order('log_id')
                ->limit(100);

            $query = $readAdapter->query($select);
            $count = 0;
            while ($row = $query->fetch()) {
                $count++;
                $customerLogId = $row['log_id'];
                if (!isset($needLogIds[$row['log_id']])) {
                    $visitorIds[] = $row['visitor_id'];
                }
            }

            if (!$count) {
                break;
            }

            if ($visitorIds) {
                $condition = ['visitor_id IN (?)' => $visitorIds];

                // remove visitors from log/quote
                $writeAdapter->delete($this->getTable('log/quote_table'), $condition);

                // remove visitors from log/url
                $writeAdapter->delete($this->getTable('log/url_table'), $condition);

                // remove visitors from log/visitor_info
                $writeAdapter->delete($this->getTable('log/visitor_info'), $condition);

                // remove visitors from log/visitor
                $writeAdapter->delete($this->getTable('log/visitor'), $condition);

                // remove customers from log/customer
                $writeAdapter->delete($this->getTable('log/customer'), $condition);
            }

            if ($customerLogId == $lastLogId) {
                break;
            }
        }

        return $this;
    }

    /**
     * Clean url table
     *
     * @return $this
     */
    protected function _cleanUrls()
    {
        $readAdapter    = $this->_getReadAdapter();
        $writeAdapter   = $this->_getWriteAdapter();

        while (true) {
            $select = $readAdapter->select()
                ->from(
                    ['url_info_table' => $this->getTable('log/url_info_table')],
                    ['url_id']
                )
                ->joinLeft(
                    ['url_table' => $this->getTable('log/url_table')],
                    'url_info_table.url_id = url_table.url_id',
                    []
                )
                ->where('url_table.url_id IS NULL')
                ->limit(100);

            $urlIds = $readAdapter->fetchCol($select);

            if (!$urlIds) {
                break;
            }

            $writeAdapter->delete(
                $this->getTable('log/url_info_table'),
                ['url_id IN (?)' => $urlIds]
            );
        }

        return $this;
    }
}
