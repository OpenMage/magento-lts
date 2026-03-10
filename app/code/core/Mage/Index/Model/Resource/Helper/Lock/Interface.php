<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Index
 */

/**
 * Abstract lock helper
 *
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
     * @param  string $name
     * @return bool
     */
    public function setLock($name);

    /**
     * Release lock
     *
     * @param  string $name
     * @return bool
     */
    public function releaseLock($name);

    /**
     * Is lock exists
     *
     * @param  string $name
     * @return bool
     */
    public function isLocked($name);

    /**
     * @return $this
     */
    public function setWriteAdapter(Varien_Db_Adapter_Interface $adapter);
}
