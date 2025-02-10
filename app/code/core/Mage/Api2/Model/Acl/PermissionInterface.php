<?php
/**
 * API2 ACL resource permission interface
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
