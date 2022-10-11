<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 Global ACL role resources permissions model
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Acl_Global_Rule_ResourcePermission implements Mage_Api2_Model_Acl_PermissionInterface
{
    /**
     * Resources permissions
     *
     * @var array
     */
    protected $_resourcesPermissions;

    /**
     * Role
     *
     * @var Mage_Api2_Model_Acl_Global_Role
     */
    protected $_role;

    /**
     * Get resources permissions for selected role
     *
     * @return array
     */
    public function getResourcesPermissions()
    {
        if ($this->_resourcesPermissions === null) {
            $roleConfigNodeName = $this->_role->getConfigNodeName();
            $rulesPairs = [];
            $allowedType = Mage_Api2_Model_Acl_Global_Rule_Permission::TYPE_ALLOW;

            if ($this->_role) {
                /** @var Mage_Api2_Model_Resource_Acl_Global_Rule_Collection $rules */
                $rules = Mage::getResourceModel('api2/acl_global_rule_collection');
                $rules->addFilterByRoleId($this->_role->getId());

                /** @var Mage_Api2_Model_Acl_Global_Rule $rule */
                foreach ($rules as $rule) {
                    $resourceId = $rule->getResourceId();
                    $rulesPairs[$resourceId]['privileges'][$roleConfigNodeName][$rule->getPrivilege()] = $allowedType;
                }
            } else {
                //make resource "all" as default for new item
                $rulesPairs = [Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL => $allowedType];
            }

            //set permissions to resources
            /** @var Mage_Api2_Model_Config $config */
            $config = Mage::getModel('api2/config');
            /** @var Mage_Api2_Model_Acl_Global_Rule_Privilege $privilegeSource */
            $privilegeSource = Mage::getModel('api2/acl_global_rule_privilege');
            $privileges = array_keys($privilegeSource::toArray());

            /** @var Varien_Simplexml_Element $node */
            foreach ($config->getResources() as $resourceType => $node) {
                $resourceId = (string)$resourceType;
                $allowedRoles = (array)$node->privileges;
                $allowedPrivileges = [];
                if (isset($allowedRoles[$roleConfigNodeName])) {
                    $allowedPrivileges = $allowedRoles[$roleConfigNodeName];
                }
                foreach ($privileges as $privilege) {
                    if (empty($allowedPrivileges[$privilege])
                        && isset($rulesPairs[$resourceId][$roleConfigNodeName]['privileges'][$privilege])
                    ) {
                        unset($rulesPairs[$resourceId][$roleConfigNodeName]['privileges'][$privilege]);
                    } elseif (!empty($allowedPrivileges[$privilege])
                        && !isset($rulesPairs[$resourceId][$roleConfigNodeName]['privileges'][$privilege])
                    ) {
                        $deniedType = Mage_Api2_Model_Acl_Global_Rule_Permission::TYPE_DENY;
                        $rulesPairs[$resourceId]['privileges'][$roleConfigNodeName][$privilege] = $deniedType;
                    }
                }
            }
            $this->_resourcesPermissions = $rulesPairs;
        }
        return $this->_resourcesPermissions;
    }

    /**
     * Set filter value
     *
     * @param Mage_Api2_Model_Acl_Global_Role $role
     */
    public function setFilterValue($role)
    {
        if ($role && $role->getId()) {
            $this->_role = $role;
        }
    }
}
