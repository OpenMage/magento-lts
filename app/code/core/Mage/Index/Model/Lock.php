<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Index
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Lock model
 *
 * @category   Mage
 * @package    Mage_Index
 */
class Mage_Index_Model_Lock
{
    /**
     * Lock storage config path
     */
    public const STORAGE_CONFIG_PATH = 'global/index/lock/storage';

    /**
     * Storage instance
     *
     * @var Mage_Index_Model_Lock_Storage_Interface
     */
    protected $_storage;

    /**
     * Singleton instance
     *
     * @var Mage_Index_Model_Lock
     */
    protected static $_instance;

    /**
     * Array of registered DB locks
     *
     * @var array
     */
    protected static $_lockDb = [];

    /**
     * Array of registered file locks
     *
     * @var array
     */
    protected static $_lockFile = [];

    /**
     * Array of registered file lock resources
     *
     * @var array
     */
    protected static $_lockFileResource = [];

    protected function __construct()
    {
        register_shutdown_function([$this, 'shutdownReleaseLocks']);
    }

    /**
     * Get lock singleton instance
     *
     * @return $this
     */
    public static function getInstance()
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Release all locks on application shutdown
     */
    public function shutdownReleaseLocks()
    {
        foreach (self::$_lockDb as $lockDb) {
            $this->_releaseLockDb($lockDb);
        }
        foreach (self::$_lockFile as $lockFile) {
            $this->_releaseLockFile($lockFile);
        }
        foreach (self::$_lockFileResource as $lockFileResource) {
            if ($lockFileResource) {
                fclose($lockFileResource);
            }
        }
    }

    /**
     * Set named lock
     *
     * @param string $lockName
     * @param bool $file
     * @param bool $block
     * @return bool
     */
    public function setLock($lockName, $file = false, $block = false)
    {
        if ($file) {
            return $this->_setLockFile($lockName, $block);
        } else {
            return $this->_setLockDb($lockName, $block);
        }
    }

    /**
     * Set named file lock
     *
     * @param string $lockName
     * @param bool $block
     * @return bool
     */
    protected function _setLockFile($lockName, $block = false)
    {
        if ($block) {
            try {
                $result = flock($this->_getLockFile($lockName), LOCK_EX);
            } catch (Exception $e) {
                Mage::logException($e);
                throw $e;
            }
        } else {
            try {
                $result = flock($this->_getLockFile($lockName), LOCK_EX | LOCK_NB);
            } catch (Exception $e) {
                Mage::logException($e);
                throw $e;
            }
        }
        if ($result) {
            self::$_lockFile[$lockName] = $lockName;
            return true;
        }
        return false;
    }

    /**
     * Set named DB lock
     *
     * @param string $lockName
     * @param bool $block
     * @return bool
     */
    protected function _setLockDb($lockName, $block = false)
    {
        if ($this->_getLockStorage()->setLock($lockName)) {
            self::$_lockDb[$lockName] = $lockName;
            return true;
        }
        return false;
    }

    /**
     * Release named lock by name
     *
     * @param string $lockName
     * @param bool $file
     * @return bool
     */
    public function releaseLock($lockName, $file = false)
    {
        if ($file) {
            return $this->_releaseLockFile($lockName);
        } else {
            return $this->_releaseLockDb($lockName);
        }
    }

    /**
     * Release named file lock by name
     *
     * @param string $lockName
     * @return bool
     */
    protected function _releaseLockFile($lockName)
    {
        if (flock($this->_getLockFile($lockName), LOCK_UN)) {
            unset(self::$_lockFile[$lockName]);
            return true;
        }
        return false;
    }

    /**
     * Release named DB lock by name
     *
     * @param string $lockName
     * @return bool
     */
    protected function _releaseLockDb($lockName)
    {
        if ($this->_getLockStorage()->releaseLock($lockName)) {
            unset(self::$_lockDb[$lockName]);
            return true;
        }
        return false;
    }

    /**
     * Check whether the named lock exists
     *
     * @param string $lockName
     * @param bool $file
     * @return bool
     */
    public function isLockExists($lockName, $file = false)
    {
        if ($file) {
            return $this->_isLockExistsFile($lockName);
        } else {
            return $this->_isLockExistsDb($lockName);
        }
    }

    /**
     * Check whether the named file lock exists
     *
     * @param string $lockName
     * @return bool
     */
    protected function _isLockExistsFile($lockName)
    {
        $result = true;
        $fp = $this->_getLockFile($lockName);
        try {
            if (flock($fp, LOCK_EX | LOCK_NB)) {
                flock($fp, LOCK_UN);
                $result = false;
            }
        } catch (Exception $e) {
            Mage::logException($e);
            throw $e;
        }

        return $result;
    }

    /**
     * Check whether the named DB lock exists
     *
     * @param string $lockName
     * @return bool
     */
    protected function _isLockExistsDb($lockName)
    {
        return (bool) $this->_getLockStorage()->isLockExists($lockName);
    }

    /**
     * Get lock storage model
     *
     * @return Mage_Index_Model_Lock_Storage_Interface
     */
    protected function _getLockStorage()
    {
        if (!$this->_storage instanceof Mage_Index_Model_Lock_Storage_Interface) {
            $config = Mage::getConfig()->getNode(self::STORAGE_CONFIG_PATH);
            $this->_storage = Mage::getModel($config->model);
        }
        return $this->_storage;
    }

    /**
     * Get lock file resource
     *
     * @param string $lockName
     * @return resource
     * @throws Exception
     */
    protected function _getLockFile($lockName)
    {
        if (!isset(self::$_lockFileResource[$lockName]) || self::$_lockFileResource[$lockName] === null) {
            $varDir = Mage::getConfig()->getVarDir('locks');
            $file = $varDir . DS . $lockName . '.lock';
            if (is_file($file)) {
                self::$_lockFileResource[$lockName] = fopen($file, 'w');
            } else {
                self::$_lockFileResource[$lockName] = fopen($file, 'x');
            }
            if (!self::$_lockFileResource[$lockName]) {
                self::$_lockFileResource[$lockName] = null;
                throw new Exception(sprintf('Unable to open lock file \'%s\': %s', $file, error_get_last()));
            }
            fwrite(self::$_lockFileResource[$lockName], date('r'));
        }
        return self::$_lockFileResource[$lockName];
    }
}
