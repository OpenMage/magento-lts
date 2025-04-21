<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Index
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
