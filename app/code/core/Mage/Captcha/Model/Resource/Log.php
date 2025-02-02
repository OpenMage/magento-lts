<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Captcha
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Log Attempts resource
 *
 * @category   Mage
 * @package    Mage_Captcha
 */
class Mage_Captcha_Model_Resource_Log extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Type Remote Address
     */
    public const TYPE_REMOTE_ADDRESS = 1;

    /**
     * Type User Login Name
     */
    public const TYPE_LOGIN = 2;

    protected function _construct()
    {
        $this->_setMainTable('captcha/log');
    }

    /**
     * Save or Update count Attempts
     *
     * @param string|null $login
     * @return $this
     */
    public function logAttempt($login)
    {
        if ($login != null) {
            $this->_getWriteAdapter()->insertOnDuplicate(
                $this->getMainTable(),
                [
                    'type' => self::TYPE_LOGIN, 'value' => $login, 'count' => 1,
                    'updated_at' => Mage::getSingleton('core/date')->gmtDate(),
                ],
                ['count' => new Zend_Db_Expr('count+1'), 'updated_at'],
            );
        }
        $ip = Mage::helper('core/http')->getRemoteAddr();
        if ($ip != null) {
            $this->_getWriteAdapter()->insertOnDuplicate(
                $this->getMainTable(),
                [
                    'type' => self::TYPE_REMOTE_ADDRESS, 'value' => $ip, 'count' => 1,
                    'updated_at' => Mage::getSingleton('core/date')->gmtDate(),
                ],
                ['count' => new Zend_Db_Expr('count+1'), 'updated_at'],
            );
        }
        return $this;
    }

    /**
     * Delete User attempts by login
     *
     * @param string $login
     * @return $this
     */
    public function deleteUserAttempts($login)
    {
        if ($login != null) {
            $this->_getWriteAdapter()->delete(
                $this->getMainTable(),
                ['type = ?' => self::TYPE_LOGIN, 'value = ?' => $login],
            );
        }
        $ip = Mage::helper('core/http')->getRemoteAddr();
        if ($ip != null) {
            $this->_getWriteAdapter()->delete(
                $this->getMainTable(),
                ['type = ?' => self::TYPE_REMOTE_ADDRESS, 'value = ?' => $ip],
            );
        }

        return $this;
    }

    /**
     * Get count attempts by ip
     *
     * @return string|int
     */
    public function countAttemptsByRemoteAddress()
    {
        $ip = Mage::helper('core/http')->getRemoteAddr();
        if (!$ip) {
            return 0;
        }
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getMainTable(), 'count')->where('type = ?', self::TYPE_REMOTE_ADDRESS)
            ->where('value = ?', $ip);
        return $read->fetchOne($select);
    }

    /**
     * Get count attempts by user login
     *
     * @param string $login
     * @return string|int
     */
    public function countAttemptsByUserLogin($login)
    {
        if (!$login) {
            return 0;
        }
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getMainTable(), 'count')->where('type = ?', self::TYPE_LOGIN)
            ->where('value = ?', $login);
        return $read->fetchOne($select);
    }

    /**
     * Delete attempts with expired in update_at time
     */
    public function deleteOldAttempts()
    {
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            ['updated_at < ?' => Mage::getSingleton('core/date')->gmtDate(null, time() - 60 * 30)],
        );
    }
}
