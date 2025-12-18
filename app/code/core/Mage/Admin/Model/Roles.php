<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

/**
 * Admin Roles Model
 *
 * @package    Mage_Admin
 *
 * @method Mage_Admin_Model_Resource_Roles _getResource()
 * @method Mage_Admin_Model_Resource_Roles_Collection getCollection()
 * @method string getName()
 * @method int getParentId()
 * @method int getPid()
 * @method Mage_Admin_Model_Resource_Roles getResource()
 * @method Mage_Admin_Model_Resource_Roles_Collection getResourceCollection()
 * @method string getRoleName()
 * @method string getRoleType()
 * @method int getSortOrder()
 * @method int getTreeLevel()
 * @method int getUserId()
 * @method $this setParentId(int $value)
 * @method $this setRoleName(string $value)
 * @method $this setRoleType(string $value)
 * @method $this setSortOrder(int $value)
 * @method $this setTreeLevel(int $value)
 * @method $this setUserId(int $value)
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
     * @return array|Varien_Simplexml_Element
     */
    public function getResourcesTree()
    {
        return $this->_buildResourcesArray(null, null, null, null, true);
    }

    /**
     * Return list of acl resources
     *
     * @return array|Varien_Simplexml_Element
     */
    public function getResourcesList()
    {
        return $this->_buildResourcesArray();
    }

    /**
     * Return list of acl resources in 2D format
     *
     * @return array|Varien_Simplexml_Element
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
     * @return array|false|Varien_Simplexml_Element
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
                    $module = (string) $resource->getAttribute('module');
                }

                if ($rawNodes) {
                    $resource->addAttribute('aclpath', $resourceName);
                    $resource->addAttribute('module_c', $module);
                }

                if (is_null($represent2Darray)) {
                    $result[$resourceName]['name']  = Mage::helper($module)->__((string) $resource->title);
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
