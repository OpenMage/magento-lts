<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_AdminNotification
 */

/**
 * AdminNotification Inbox model
 *
 * @package    Mage_AdminNotification
 */
class Mage_AdminNotification_Model_Resource_Inbox extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('adminnotification/inbox', 'notification_id');
    }

    /**
     * Load latest notice
     *
     * @return $this
     * @throws Mage_Core_Exception
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
     * @return array
     * @throws Mage_Core_Exception
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
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Adapter_Exception
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
