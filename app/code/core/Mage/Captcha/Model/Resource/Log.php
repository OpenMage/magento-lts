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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Log Attempts resource
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Captcha_Model_Resource_Log extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Type Remote Address
     */
    const TYPE_REMOTE_ADDRESS = 1;

    /**
     * Type User Login Name
     */
    const TYPE_LOGIN = 2;

    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_setMainTable('captcha/log');
    }

    /**
     * Save or Update count Attempts
     *
     * @param string|null $login
     * @return Mage_Captcha_Model_Resource_Log
     */
    public function logAttempt($login)
    {
        if ($login != null){
            $this->_getWriteAdapter()->insertOnDuplicate(
                $this->getMainTable(),
                array(
                     'type' => self::TYPE_LOGIN, 'value' => $login, 'count' => 1,
                     'updated_at' => Mage::getSingleton('core/date')->gmtDate()
                ),
                array('count' => new Zend_Db_Expr('count+1'), 'updated_at')
            );
        }
        $ip = Mage::helper('core/http')->getRemoteAddr();
        if ($ip != null) {
            $this->_getWriteAdapter()->insertOnDuplicate(
                $this->getMainTable(),
                array(
                     'type' => self::TYPE_REMOTE_ADDRESS, 'value' => $ip, 'count' => 1,
                     'updated_at' => Mage::getSingleton('core/date')->gmtDate()
                ),
                array('count' => new Zend_Db_Expr('count+1'), 'updated_at')
            );
        }
        return $this;
    }

    /**
     * Delete User attempts by login
     *
     * @param string $login
     * @return Mage_Captcha_Model_Resource_Log
     */
    public function deleteUserAttempts($login)
    {
        if ($login != null) {
            $this->_getWriteAdapter()->delete(
                $this->getMainTable(),
                array('type = ?' => self::TYPE_LOGIN, 'value = ?' => $login)
            );
        }
        $ip = Mage::helper('core/http')->getRemoteAddr();
        if ($ip != null) {
            $this->_getWriteAdapter()->delete(
                $this->getMainTable(), array('type = ?' => self::TYPE_REMOTE_ADDRESS, 'value = ?' => $ip)
            );
        }

        return $this;
    }

    /**
     * Get count attempts by ip
     *
     * @return null|int
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
     * @return null|int
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
     *
     * @return void
     */
    public function deleteOldAttempts()
    {
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            array('updated_at < ?' => Mage::getSingleton('core/date')->gmtDate(null, time() - 60*30))
        );
    }
}
