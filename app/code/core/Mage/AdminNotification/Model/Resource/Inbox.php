<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * AdminNotification Inbox model
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_AdminNotification_Model_Resource_Inbox extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * AdminNotification Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('adminnotification/inbox', 'notification_id');
    }

    /**
     * Load latest notice
     *
     * @param Mage_AdminNotification_Model_Inbox $object
     * @return $this
     */
    public function loadLatestNotice(Mage_AdminNotification_Model_Inbox $object)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->order($this->getIdFieldName() . ' DESC')
            ->where('is_read != 1')
            ->where('is_remove != 1')
            ->limit(1);
        $data = $adapter->fetchRow($select);

        if ($data) {
            $object->setData($data);
        }

        $this->_afterLoad($object);

        return $this;
    }

    /**
     * Get notifications grouped by severity
     *
     * @param Mage_AdminNotification_Model_Inbox $object
     * @return array
     */
    public function getNoticeStatus(Mage_AdminNotification_Model_Inbox $object)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), [
                'severity'     => 'severity',
                'count_notice' => new Zend_Db_Expr('COUNT(' . $this->getIdFieldName() . ')')])
            ->group('severity')
            ->where('is_remove=?', 0)
            ->where('is_read=?', 0);
        return $adapter->fetchPairs($select);
    }

    /**
     * Save notifications (if not exists)
     *
     * @param Mage_AdminNotification_Model_Inbox $object
     * @param array $data
     */
    public function parse(Mage_AdminNotification_Model_Inbox $object, array $data)
    {
        $adapter = $this->_getWriteAdapter();
        foreach ($data as $item) {
            $select = $adapter->select()
                ->from($this->getMainTable())
                ->where('title = ?', $item['title']);

            if (empty($item['url'])) {
                $select->where('url IS NULL');
            } else {
                $select->where('url = ?', $item['url']);
            }

            if (isset($item['internal'])) {
                $row = false;
                unset($item['internal']);
            } else {
                $row = $adapter->fetchRow($select);
            }

            if (!$row) {
                $adapter->insert($this->getMainTable(), $item);
            }
        }
    }
}
