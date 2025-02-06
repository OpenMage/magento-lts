<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Index
 */

/**
 * Abstract lock helper
 *
 * @category   Mage
 * @package    Mage_Index
 */
interface Mage_Index_Model_Resource_Helper_Lock_Interface
{
    /**
     * Timeout for lock get proc.
     */
    public const LOCK_GET_TIMEOUT = 5;

    /**
     * Set lock
     *
     * @param string $name
     * @return bool
     */
    public function setLock($name);

    /**
     * Release lock
     *
     * @param string $name
     * @return bool
     */
    public function releaseLock($name);

    /**
     * Is lock exists
     *
     * @param string $name
     * @return bool
     */
    public function isLocked($name);

    /**
     * @return $this
     */
    public function setWriteAdapter(Varien_Db_Adapter_Interface $adapter);
}
