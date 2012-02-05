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
 * @package     Mage_Captcha
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Login Attempt resource
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Captcha_Model_Resource_LoginAttempt extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('captcha/login_attempt', array('type','value'));
    }

    /**
     * Log Login
     *
     * @param string|null $login
     * @return Mage_Captcha_Model_Resource_LoginAttempt
     */
    public function logUserLogin($login){
        if ($login != null){

            $this->_getWriteAdapter()->insertOnDuplicate(
                $this->getMainTable(),
                array(
                     'type' => Mage_Captcha_Model_LoginAttempt::TYPE_LOGIN,
                     'value' => md5($login), 'count' => 1, 'updated_at' => Mage::getSingleton('core/date')->gmtDate()
                ),
                array('count' => new Zend_Db_Expr('count+1'), 'updated_at')
            );
        }
        return $this;
    }

    /**
     * Log Ip
     *
     * @param string|null $ip
     * @return Mage_Captcha_Model_Resource_LoginAttempt
     */
    public function logRemoteAddress($ip){
        if ($ip != null) {
            $this->_getWriteAdapter()->insertOnDuplicate(
                $this->getMainTable(),
                array(
                     'type' => Mage_Captcha_Model_LoginAttempt::TYPE_REMOTE_ADDRESS,
                     'value' => md5($ip), 'count' => 1, 'updated_at' => Mage::getSingleton('core/date')->gmtDate()
                ),
                array('count' => new Zend_Db_Expr('count+1'), 'updated_at')
            );
        }
        return $this;
    }

    /**
     * Delete attempts by remote address
     * @param $ip
     * @return Mage_Captcha_Model_Resource_LoginAttempt
     */
    public function deleteByRemoteAddress($ip){
        if ($ip != null) {
            $this->_getWriteAdapter()->delete(
                $this->getMainTable(),
                array('type = ?' => Mage_Captcha_Model_LoginAttempt::TYPE_REMOTE_ADDRESS, 'value = ?' => md5($ip))
            );
        }
        return $this;
    }

    /**
     * Delete attempts by login
     *
     * @param $login
     * @return Mage_Captcha_Model_Resource_LoginAttempt
     */
    public function deleteByUserName($login){
        if ($login != null) {
            $this->_getWriteAdapter()->delete(
                $this->getMainTable(),
                array('type = ?' => Mage_Captcha_Model_LoginAttempt::TYPE_LOGIN, 'value = ?' => md5($login))
            );
        }
        return $this;
    }

    /**
     * Get count attempts by ip
     *
     * @param string $ip
     * @return null|Mage_Captcha_Model_LoginAttempt
     */
    public function countAttemptsByRemoteAddress($ip){
        if (!$ip) {
            return 0;
        }
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getMainTable(), 'count')
            ->where('type = ?', Mage_Captcha_Model_LoginAttempt::TYPE_REMOTE_ADDRESS)
            ->where('value = ?', md5($ip));
        return $read->fetchOne($select);
    }

    /**
     * Get count attempts by user login
     *
     * @param string $login
     * @return null|Mage_Captcha_Model_LoginAttempt
     */
    public function countAttemptsByUserLogin($login){
        if (!$login) {
            return 0;
        }
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getMainTable(), 'count')
            ->where('type = ?', Mage_Captcha_Model_LoginAttempt::TYPE_LOGIN)
            ->where('value = ?', md5($login));
        return $read->fetchOne($select);
    }

    public function deleteOldAttempts(){
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            array('updated_at < ?' => Mage::getSingleton('core/date')->gmtDate(null, time() - 60*30))
        );
    }
}
