<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 acl global rule tree
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Acl_Global_Rule_Tree extends Mage_Core_Helper_Abstract
{
    /**
     * Tree types
     */
    public const TYPE_ATTRIBUTE = 'attribute';
    public const TYPE_PRIVILEGE = 'privilege';

    /**
     * Names
     */
    public const NAME_CHILDREN         = 'children';
    public const NAME_PRIVILEGE        = 'privilege';
    public const NAME_OPERATION        = 'operation';
    public const NAME_ATTRIBUTE        = 'attribute';
    public const NAME_RESOURCE         = 'resource';
    public const NAME_RESOURCE_GROUPS  = 'resource_groups';
    public const NAME_GROUP            = 'group';

    /**
     * Separator for tree ID
     */
    public const ID_SEPARATOR = '-';

    /**
     * Role
     *
     * @var Mage_Api2_Model_Acl_Global_Role
     */
    protected $_role;

    /**
     * Resources permissions
     *
     * @var array
     */
    protected $_resourcesPermissions;

    /**
     * Resources from config model
     *
     * @var Varien_Simplexml_Element
     */
    protected $_resourcesConfig;

    /**
     * Exist privileges
     *
     * @var array
     */
    protected $_existPrivileges;

    /**
     * Exist operations
     *
     * @var array
     */
    protected $_existOperations;

    /**
     * Tree type
     *
     * @var string
     */
    protected $_type;

    /**
     * Initialized
     *
     * @var bool
     */
    protected $_initialized = false;

    /**
     * Flag if resource has entity only attributes
     *
     * @var bool
     */
    protected $_hasEntityOnlyAttributes = false;

    /**
     * Constructor
     *
     * In the constructor should be set tree type: attributes or privileges.
     * Attributes for tree with resources, operations and attributes.
     * Privileges for tree with resources and privileges.
     *
     * @param array $options
     */
    public function __construct($options)
    {
        $this->_type = $options['type'];

        switch ($this->_type) {
            case self::TYPE_ATTRIBUTE:
                /** @var Mage_Api2_Model_Acl_Filter_Attribute_Operation $operationSource */
                $operationSource = Mage::getModel('api2/acl_filter_attribute_operation');
                $this->_existOperations = $operationSource->toArray();
                break;

            case self::TYPE_PRIVILEGE:
                /** @var Mage_Api2_Model_Acl_Global_Rule_Privilege $privilegeSource */
                $privilegeSource = Mage::getModel('api2/acl_global_rule_privilege');
                $this->_existPrivileges = $privilegeSource->toArray();
                break;

            default:
                throw new Exception(sprintf('Unknown tree type "%s".', $this->_type));
        }
    }

    /**
     * Initialize block
     *
     * @return $this
     * @throws Exception
     */
    protected function _init()
    {
        if ($this->_initialized) {
            return $this;
        }

        /** @var Mage_Api2_Model_Config $config */
        $config = Mage::getModel('api2/config');
        $this->_resourcesConfig = $config->getResourceGroups();

        if ($this->_type == self::TYPE_ATTRIBUTE && !$this->_existOperations) {
            throw new Exception('Operations is not set');
        }

        if ($this->_type == self::TYPE_PRIVILEGE && !$this->_existPrivileges) {
            throw new Exception('Privileges is not set.');
        }

        return $this;
    }

    /**
     * Convert to array serialized post data from tree grid
     *
     * @return array
     */
    public function getPostResources()
    {
        $isAll = Mage::app()->getRequest()->getParam(Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL);
        $allow = Mage_Api2_Model_Acl_Global_Rule_Permission::TYPE_ALLOW;
        if ($isAll) {
            $resources = [
                Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL => [
                    null => $allow,
                ],
            ];
        } else {
            $resources = [];
            $checkedResources = explode(',', Mage::app()->getRequest()->getParam('resource'));
            $prefixResource  = self::NAME_RESOURCE . self::ID_SEPARATOR;
            switch ($this->_type) {
                case self::TYPE_PRIVILEGE:
                    $prefixPrivilege = self::NAME_PRIVILEGE . self::ID_SEPARATOR;
                    $nameResource = null;
                    foreach ($checkedResources as $i => $item) {
                        if (str_starts_with($item, $prefixResource)) {
                            $nameResource = substr($item, mb_strlen($prefixResource, 'UTF-8'));
                            $resources[$nameResource] = [];
                        } elseif (str_starts_with($item, $prefixPrivilege)) {
                            $name = substr($item, mb_strlen($prefixPrivilege, 'UTF-8'));
                            $namePrivilege = str_replace($nameResource . self::ID_SEPARATOR, '', $name);
                            $resources[$nameResource][$namePrivilege] = $allow;
                        } else {
                            unset($checkedResources[$i]);
                        }
                    }
                    break;

                case self::TYPE_ATTRIBUTE:
                    $prefixOperation = self::NAME_OPERATION . self::ID_SEPARATOR;
                    $prefixAttribute = self::NAME_ATTRIBUTE . self::ID_SEPARATOR;
                    $nameResource = null;
                    foreach ($checkedResources as $i => $item) {
                        if (str_starts_with($item, $prefixResource)) {
                            $nameResource = substr($item, mb_strlen($prefixResource, 'UTF-8'));
                            $resources[$nameResource] = [];
                        } elseif (str_starts_with($item, $prefixOperation)) {
                            $name = substr($item, mb_strlen($prefixOperation, 'UTF-8'));
                            $operationName = str_replace($nameResource . self::ID_SEPARATOR, '', $name);
                            $resources[$nameResource][$operationName] = [];
                        } elseif (str_starts_with($item, $prefixAttribute)) {
                            $name = substr($item, mb_strlen($prefixOperation, 'UTF-8'));
                            $attributeName = str_replace(
                                $nameResource . self::ID_SEPARATOR . $operationName . self::ID_SEPARATOR,
                                '',
                                $name,
                            );
                            $resources[$nameResource][$operationName][$attributeName] = $allow;
                        } else {
                            unset($checkedResources[$i]);
                        }
                    }
                    break;

                    //no default
            }
        }
        return $resources;
    }

    /**
     * Check if everything is allowed
     *
     * @return bool
     */
    public function getEverythingAllowed()
    {
        $this->_init();

        $all = Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL;
        return !empty($this->_resourcesPermissions[$all]);
    }

    /**
     * Get tree resources
     *
     * @return array
     */
    public function getTreeResources()
    {
        $this->_init();
        $root = $this->_getTreeNode($this->_resourcesConfig, 1);
        return $root[self::NAME_CHILDREN] ?? [];
    }

    /**
     * Get tree node
     *
     * @param Varien_Simplexml_Element|array $node
     * @param int $level
     * @return array|null
     */
    protected function _getTreeNode($node, $level = 0)
    {
        $item = [];

        $isResource = false;
        $isGroup    = false;
        $name       = null;

        if ($level != 0) {
            $name = $node->getName();
            if (!(int) $node->resource) {
                if (self::NAME_RESOURCE_GROUPS != $name) {
                    $isGroup = true;
                    $item['id'] = self::NAME_GROUP . self::ID_SEPARATOR . $name;
                }
                $item['text'] = (string) $node->title;
            } else {
                $isResource = true;
                $item['id'] = self::NAME_RESOURCE . self::ID_SEPARATOR . $name;
                $item['text'] = $this->__('%s', (string) $node->title);
            }
            $item['checked'] = false;
            $item['sort_order'] = isset($node->sort_order) ? (string) $node->sort_order : 0;
        }
        if (isset($node->children)) {
            $children = $node->children->children();
        } else {
            $children = $node->children();
        }

        if (empty($children)) {
            /**
             * Node doesn't have any child nodes
             * and it should be skipped
             */
            return $item;
        }

        $item[self::NAME_CHILDREN] = [];

        if ($isResource) {
            if (self::TYPE_ATTRIBUTE == $this->_type) {
                if (!$this->_addOperations($item, $node, $name)) {
                    return null;
                }
            } elseif (self::TYPE_PRIVILEGE == $this->_type) {
                if (!$this->_addPrivileges($item, $node, $name)) {
                    return null;
                }
            }
        }

        /** @var Varien_Simplexml_Element $child */
        foreach ($children as $child) {
            if ($child->getName() != 'title' && $child->getName() != 'sort_order') {
                if (!(string) $child->title) {
                    continue;
                }

                if ($level != 0) {
                    $subNode = $this->_getTreeNode($child, $level + 1);
                    if (!$subNode) {
                        continue;
                    }
                    //if sub-node check then check current node
                    if (!empty($subNode['checked'])) {
                        $item['checked'] = true;
                    }
                    $item[self::NAME_CHILDREN][] = $subNode;
                } else {
                    $item = $this->_getTreeNode($child, $level + 1);
                }
            }
        }
        if (!empty($item[self::NAME_CHILDREN])) {
            usort($item[self::NAME_CHILDREN], [$this, '_sortTree']);
        } elseif ($isGroup) {
            //skip empty group
            return null;
        }
        return $item;
    }

    /**
     * Add privileges
     *
     * @param array $item                       Tree node
     * @param Varien_Simplexml_Element $node    XML node
     * @param string $name                      Resource name
     * @return bool
     */
    protected function _addPrivileges(&$item, Varien_Simplexml_Element $node, $name)
    {
        $roleConfigNodeName = $this->getRole()->getConfigNodeName();
        $possibleList = [];
        if (isset($node->privileges)) {
            $possibleRoles = $node->privileges->asArray();
            if (isset($possibleRoles[$roleConfigNodeName])) {
                $possibleList = $possibleRoles[$roleConfigNodeName];
            }
        }

        if (!$possibleList) {
            return false;
        }

        $cnt = 0;
        foreach ($this->_existPrivileges as $key => $title) {
            if (empty($possibleList[$key])) {
                continue;
            }
            $checked = !empty($this->_resourcesPermissions[$name]['privileges'][$roleConfigNodeName][$key]);
            $item['checked'] = $checked ? $checked : $item['checked'];
            $subItem = [
                'id' => self::NAME_PRIVILEGE . self::ID_SEPARATOR . $name . self::ID_SEPARATOR . $key,
                'text' => $title,
                'checked' => $checked,
                'sort_order' => ++$cnt,
            ];
            $item[self::NAME_CHILDREN][] = $subItem;
        }
        return true;
    }

    /**
     * Add operation
     *
     * @param array $item                       Tree node
     * @param Varien_Simplexml_Element $node    XML node
     * @param string $name                      Resource name
     * @return bool
     */
    protected function _addOperations(&$item, Varien_Simplexml_Element $node, $name)
    {
        $cnt = 0;
        foreach ($this->_existOperations as $key => $title) {
            $subItem = [
                'id' => self::NAME_OPERATION . self::ID_SEPARATOR . $name . self::ID_SEPARATOR . $key,
                'text' => $title,
                'checked' => false,
                'sort_order' => ++$cnt,
            ];

            if (!empty($this->_resourcesPermissions[$name]['operations'][$key]['attributes'])) {
                if (!$this->_addAttribute($subItem, $node, $name, $key)) {
                    $cnt--;
                    continue;
                }
            } else {
                $cnt--;
                continue;
            }
            if (!empty($subItem['checked'])) {
                $item['checked'] = true;
            }
            $item[self::NAME_CHILDREN][] = $subItem;
        }
        if (!$cnt) {
            return false;
        }
        return true;
    }

    /**
     * Add privileges
     *
     * @param array $item Tree node
     * @param Varien_Simplexml_Element $node XML node
     * @param string $name Node name
     * @param string $privilege Privilege name
     * @return bool
     */
    protected function _addAttribute(&$item, Varien_Simplexml_Element $node, $name, $privilege)
    {
        $cnt = 0;
        foreach ($this->_resourcesPermissions[$name]['operations'][$privilege]['attributes'] as $key => $attribute) {
            $title = $attribute['title'];
            $status = $attribute['status'];

            $checked = $status == Mage_Api2_Model_Acl_Global_Rule_Permission::TYPE_ALLOW;
            $item['checked'] = $checked ? $checked : $item['checked'];
            $item[self::NAME_CHILDREN][] = [
                'id' => self::NAME_ATTRIBUTE . self::ID_SEPARATOR . $name . self::ID_SEPARATOR . $privilege
                    . self::ID_SEPARATOR . $key,
                'text' => $title,
                'checked' => $checked,
                'sort_order' => ++$cnt,
            ];
        }

        return true;
    }

    /**
     * Compare two nodes of the Resource Tree
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _sortTree($a, $b)
    {
        return $a['sort_order'] < $b['sort_order'] ? -1 : ($a['sort_order'] > $b['sort_order'] ? 1 : 0);
    }

    /**
     * Set role
     *
     * @param Mage_Api2_Model_Acl_Global_Role $role
     * @return $this
     */
    public function setRole($role)
    {
        $this->_role = $role;
        return $this;
    }

    /**
     * Get role
     *
     * @return Mage_Api2_Model_Acl_Global_Role
     */
    public function getRole()
    {
        return $this->_role;
    }

    /**
     * Set resources permissions
     *
     * @param array $resourcesPermissions
     * @return $this
     */
    public function setResourcesPermissions($resourcesPermissions)
    {
        $this->_resourcesPermissions = $resourcesPermissions;
        return $this;
    }

    /**
     * Get resources permissions
     *
     * @return array
     */
    public function getResourcesPermissions()
    {
        return $this->_resourcesPermissions;
    }

    /**
     * Set has entity only attributes flag
     *
     * @param bool $hasEntityOnlyAttributes
     * @return $this
     */
    public function setHasEntityOnlyAttributes($hasEntityOnlyAttributes)
    {
        $this->_hasEntityOnlyAttributes = $hasEntityOnlyAttributes;
        return $this;
    }

    /**
     * Get has entity only attributes flag
     *
     * @return bool
     */
    public function getHasEntityOnlyAttributes()
    {
        return $this->_hasEntityOnlyAttributes;
    }
}
