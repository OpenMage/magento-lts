<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin Roles Model
 *
 * @category   Mage
 * @package    Mage_Admin
 *
 * @method Mage_Admin_Model_Resource_Roles _getResource()
 * @method Mage_Admin_Model_Resource_Roles getResource()
 * @method Mage_Admin_Model_Resource_Roles_Collection getResourceCollection()
 *
 * @method int getParentId()
 * @method $this setParentId(int $value)
 * @method int getTreeLevel()
 * @method $this setTreeLevel(int $value)
 * @method int getSortOrder()
 * @method $this setSortOrder(int $value)
 * @method string getRoleType()
 * @method $this setRoleType(string $value)
 * @method int getUserId()
 * @method $this setUserId(int $value)
 * @method string getRoleName()
 * @method $this setRoleName(string $value)
 * @method string getName()
 * @method int getPid()
 */
class Mage_Admin_Model_Roles extends Mage_Core_Model_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'admin_roles';

    protected function _construct()
    {
        $this->_init('admin/roles');
    }

    /**
     * Update object into database
     *
     * @return $this
     */
    public function update()
    {
        $this->getResource()->update($this);
        return $this;
    }

    /**
     * Retrieve users collection
     *
     * @return Mage_Admin_Model_Resource_Roles_User_Collection
     */
    public function getUsersCollection()
    {
        return Mage::getResourceModel('admin/roles_user_collection');
    }

    /**
     * Return tree of acl resources
     *
     * @return Varien_Simplexml_Element|array
     */
    public function getResourcesTree()
    {
        return $this->_buildResourcesArray(null, null, null, null, true);
    }

    /**
     * Return list of acl resources
     *
     * @return Varien_Simplexml_Element|array
     */
    public function getResourcesList()
    {
        return $this->_buildResourcesArray();
    }

    /**
     * Return list of acl resources in 2D format
     *
     * @return Varien_Simplexml_Element|array
     */
    public function getResourcesList2D()
    {
        return $this->_buildResourcesArray(null, null, null, true);
    }

    /**
     * Return users for role
     *
     * @return array|false
     */
    public function getRoleUsers()
    {
        return $this->getResource()->getRoleUsers($this);
    }

    /**
     * Build resources array process
     *
     * @param  null|string $parentName
     * @param  null|int $level
     * @param  null|mixed $represent2Darray
     * @param  bool $rawNodes
     * @param  string $module
     * @return Varien_Simplexml_Element|false|array
     */
    protected function _buildResourcesArray(
        ?Varien_Simplexml_Element $resource = null,
        $parentName = null,
        $level = 0,
        $represent2Darray = null,
        $rawNodes = false,
        $module = 'adminhtml'
    ) {
        static $result;
        if (is_null($resource)) {
            $resource = Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode('acl/resources');
            $resourceName = null;
            $level = -1;
        } else {
            $resourceName = $parentName;
            if (!empty($resource->children()) && $resource->getName() !== 'children') {
                $resourceName = (is_null($parentName) ? '' : $parentName . '/') . $resource->getName();

                //assigning module for its' children nodes
                if ($resource->getAttribute('module')) {
                    $module = (string)$resource->getAttribute('module');
                }

                if ($rawNodes) {
                    $resource->addAttribute('aclpath', $resourceName);
                    $resource->addAttribute('module_c', $module);
                }

                if (is_null($represent2Darray)) {
                    $result[$resourceName]['name']  = Mage::helper($module)->__((string)$resource->title);
                    $result[$resourceName]['level'] = $level;
                } else {
                    $result[] = $resourceName;
                }
            }
        }

        //check children and run recursion if they exists
        $children = $resource->children();
        foreach ($children as $key => $child) {
            if ($child->disabled == 1) {
                $resource->{$key} = null;
                continue;
            }
            $this->_buildResourcesArray($child, $resourceName, $level + 1, $represent2Darray, $rawNodes, $module);
        }

        if ($rawNodes) {
            return $resource;
        } else {
            return $result;
        }
    }
}
