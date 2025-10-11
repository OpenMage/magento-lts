<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Index
 */

/**
 * Abstract lock storage
 *
 * @package    Mage_Index
 */
interface Mage_Index_Model_Lock_Storage_Interface
{
    /**
     * Set named lock
     *
     * @param string $lockName
     * @return bool
     */
    public function setLock($lockName);

    /**
     * Release named lock
     *
     * @param string $lockName
     * @return bool
     */
    public function releaseLock($lockName);

    /**
     * Check whether the lock exists
     *
     * @param string $lockName
     * @return bool
     */
    public function isLockExists($lockName);
}
