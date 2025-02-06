<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Api2
 */

/**
 * API2 ACL resource permission interface
 *
 * @category   Mage
 * @package    Mage_Api2
 */
interface Mage_Api2_Model_Acl_PermissionInterface
{
    /**
     * Get ACL resources permissions
     *
     * Get permissions list with set permissions
     *
     * @return array
     */
    public function getResourcesPermissions();

    /**
     * Set filter value
     *
     * @param mixed $filterValue
     * @return Mage_Api2_Model_Acl_PermissionInterface
     */
    public function setFilterValue($filterValue);
}
