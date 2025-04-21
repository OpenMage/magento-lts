<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Index
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
