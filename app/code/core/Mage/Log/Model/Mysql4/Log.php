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
 * @package     Mage_Log
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Log Resource Model
 *
 * @category   Mage
 * @package    Mage_Log
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Log_Model_Mysql4_Log extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Init Resource model and connection
     *
     */
    protected function _construct()
    {
        $this->_init('log/visitor', 'visitor_id');
    }

    /**
     * Clean logs
     *
     * @param Mage_Log_Model_Log $object
     * @return Mage_Log_Model_Mysql4_Log
     */
    public function clean(Mage_Log_Model_Log $object)
    {
        $cleanTime = $object->getLogCleanTime();

        Mage::dispatchEvent('log_log_clean_before', array(
            'log'   => $object
        ));

        $this->_cleanVisitors($cleanTime);
        $this->_cleanCustomers($cleanTime);
        $this->_cleanUrls();

        Mage::dispatchEvent('log_log_clean_after', array(
            'log'   => $object
        ));

        return $this;
    }

    /**
     * Clean visitors table
     *
     * @param int $time
     * @return Mage_Log_Model_Mysql4_Log
     */
    protected function _cleanVisitors($time)
    {
        while (true) {
            $select = $this->_getReadAdapter()->select()
            ->from(
                array('visitor_table' => $this->getTable('log/visitor')),
                array('visitor_id' => 'visitor_table.visitor_id'))
            ->joinLeft(
                array('customer_table' => $this->getTable('log/customer')),
                'visitor_table.visitor_id = customer_table.visitor_id AND customer_table.log_id IS NULL',
                array())
            ->where('visitor_table.last_visit_at < ?', gmdate('Y-m-d H:i:s', time() - $time))
            ->limit(100);

            $query = $this->_getReadAdapter()->query($select);
            $visitorIds = array();
            while ($row = $query->fetch()) {
                $visitorIds[] = $row['visitor_id'];
            }

            if (!$visitorIds) {
                break;
            }

            // remove visitors from log/quote
            $this->_getWriteAdapter()->delete(
                $this->getTable('log/quote_table'),
                $this->_getWriteAdapter()->quoteInto('visitor_id IN(?)', $visitorIds)
            );

            // remove visitors from log/url
            $this->_getWriteAdapter()->delete(
                $this->getTable('log/url_table'),
                $this->_getWriteAdapter()->quoteInto('visitor_id IN(?)', $visitorIds)
            );

            // remove visitors from log/visitor_info
            $this->_getWriteAdapter()->delete(
                $this->getTable('log/visitor_info'),
                $this->_getWriteAdapter()->quoteInto('visitor_id IN(?)', $visitorIds)
            );

            // remove visitors from log/visitor
            $this->_getWriteAdapter()->delete(
                $this->getTable('log/visitor'),
                $this->_getWriteAdapter()->quoteInto('visitor_id IN(?)', $visitorIds)
            );
        }

        return $this;
    }

    /**
     * Clean customer table
     *
     * @param int $time
     * @return Mage_Log_Model_Mysql4_Log
     */
    protected function _cleanCustomers($time)
    {
        // retrieve last active customer log id
        $row = $this->_getReadAdapter()->fetchRow(
            $this->_getReadAdapter()->select()
                ->from($this->getTable('log/customer'), 'log_id')
                ->where('login_at < ?', gmdate('Y-m-d H:i:s', time() - $time))
                ->order('log_id DESC')
                ->limit(1)
        );

        if (!$row) {
            return $this;
        }

        $lastLogId = $row['log_id'];

        // Order by desc log_id before grouping (within-group aggregates query pattern)
        $select = $this->_getReadAdapter()->select()
            ->from(
                array('log_customer_main' => $this->getTable('log/customer')),
                array('log_id'))
            ->joinLeft(
                array('log_customer' => $this->getTable('log/customer')),
                'log_customer_main.customer_id = log_customer.customer_id AND log_customer_main.log_id < log_customer.log_id',
                array())
            ->where('log_customer.customer_id IS NULL')
            ->where('log_customer_main.log_id<?', $lastLogId + 1);

        $needLogIds = array();
        $query = $this->_getReadAdapter()->query($select);
        while ($row = $query->fetch()) {
            $needLogIds[$row['log_id']] = 1;
        }

        $customerLogId = 0;
        while (true) {
            $visitorIds = array();
            $select = $this->_getReadAdapter()->select()
                ->from(
                    $this->getTable('log/customer'),
                    array('log_id', 'visitor_id'))
                ->where('log_id>?', $customerLogId)
                ->where('log_id<?', $lastLogId + 1)
                ->order('log_id')
                ->limit(100);

            $query = $this->_getReadAdapter()->query($select);
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
                // remove visitors from log/quote
                $this->_getWriteAdapter()->delete(
                    $this->getTable('log/quote_table'),
                    $this->_getWriteAdapter()->quoteInto('visitor_id IN(?)', $visitorIds)
                );

                // remove visitors from log/url
                $this->_getWriteAdapter()->delete(
                    $this->getTable('log/url_table'),
                    $this->_getWriteAdapter()->quoteInto('visitor_id IN(?)', $visitorIds)
                );

                // remove visitors from log/visitor_info
                $this->_getWriteAdapter()->delete(
                    $this->getTable('log/visitor_info'),
                    $this->_getWriteAdapter()->quoteInto('visitor_id IN(?)', $visitorIds)
                );

                // remove visitors from log/visitor
                $this->_getWriteAdapter()->delete(
                    $this->getTable('log/visitor'),
                    $this->_getWriteAdapter()->quoteInto('visitor_id IN(?)', $visitorIds)
                );

                // remove customers from log/customer
                $this->_getWriteAdapter()->delete(
                    $this->getTable('log/customer'),
                    $this->_getWriteAdapter()->quoteInto('visitor_id IN(?)', $visitorIds)
                );
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
     * @return Mage_Log_Model_Mysql4_Log
     */
    protected function _cleanUrls()
    {
        while (true) {
            $urlIds = array();
            $select = $this->_getReadAdapter()->select()
                ->from(
                    array('url_info_table' => $this->getTable('log/url_info_table')),
                    array('url_id'))
                ->joinLeft(
                    array('url_table' => $this->getTable('log/url_table')),
                    'url_info_table.url_id = url_table.url_id',
                    array())
                ->where('url_table.url_id IS NULL')
                ->limit(100);
            $query = $this->_getReadAdapter()->query($select);
            while ($row = $query->fetch()) {
                $urlIds[] = $row['url_id'];
            }

            if (!$urlIds) {
                break;
            }

            $this->_getWriteAdapter()->delete(
                $this->getTable('log/url_info_table'),
                $this->_getWriteAdapter()->quoteInto('url_id IN(?)', $urlIds)
            );
        }
    }
}
