<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Api
 *
 * @method Mage_Api_Model_Resource_Roles _getResource()
 * @method Mage_Api_Model_Resource_Roles getResource()
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
 * @method $this setName(string $name)
 * @method int getPid()
 */
class Mage_Api_Model_Roles extends Mage_Core_Model_Abstract
{
    /**
     * Filters
     *
     * @var array
     */
    protected $_filters;

    protected function _construct()
    {
        $this->_init('api/roles');
    }

    /**
     * @return $this
     */
    public function update()
    {
        $this->getResource()->update($this);
        return $this;
    }

    /**
     * @return Mage_Api_Model_Resource_Roles_User_Collection
     */
    public function getUsersCollection()
    {
        return Mage::getResourceModel('api/roles_user_collection');
    }

    /**
     * @return array|false|Varien_Simplexml_Element
     */
    public function getResourcesTree()
    {
        return $this->_buildResourcesArray(null, null, 0, null, true);
    }

    /**
     * @return array|false|Varien_Simplexml_Element
     */
    public function getResourcesList()
    {
        return $this->_buildResourcesArray();
    }

    /**
     * @return array|false|Varien_Simplexml_Element
     */
    public function getResourcesList2D()
    {
        return $this->_buildResourcesArray(null, null, 0, true);
    }

    /**
     * @return array
     */
    public function getRoleUsers()
    {
        return $this->getResource()->getRoleUsers($this);
    }

    /**
     * @param string|null $parentName
     * @param int $level
     * @param bool|null $represent2Darray
     * @param bool $rawNodes
     * @param string $module
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
            $resource = Mage::getSingleton('api/config')->getNode('acl/resources');
            $resourceName = null;
            $level = -1;
        } else {
            $resourceName = $parentName;
            if ($resource->getName() != 'title' && $resource->getName() != 'sort_order'
                && $resource->getName() != 'children'
            ) {
                $resourceName = (is_null($parentName) ? '' : $parentName . '/') . $resource->getName();

                //assigning module for its' children nodes
                if ($resource->getAttribute('module')) {
                    $module = (string) $resource->getAttribute('module');
                }

                if ($rawNodes) {
                    $resource->addAttribute('aclpath', $resourceName);
                }

                $resource->title = Mage::helper($module)->__((string) $resource->title);

                if (is_null($represent2Darray)) {
                    $result[$resourceName]['name']  = (string) $resource->title;
                    $result[$resourceName]['level'] = $level;
                } else {
                    $result[] = $resourceName;
                }
            }
        }

        $children = $resource->children();
        if (empty($children)) {
            if ($rawNodes) {
                return $resource;
            } else {
                return $result;
            }
        }
        foreach ($children as $child) {
            $this->_buildResourcesArray($child, $resourceName, $level + 1, $represent2Darray, $rawNodes, $module);
        }
        if ($rawNodes) {
            return $resource;
        } else {
            return $result;
        }
    }

    /**
     * Filter data before save
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        $this->filter();
        parent::_beforeSave();
        return $this;
    }

    /**
     * Filter set data
     *
     * @return $this
     */
    public function filter()
    {
        $data = $this->getData();
        if (!$this->_filters || !$data) {
            return $this;
        }
        /** @var Mage_Core_Model_Input_Filter $filter */
        $filter = Mage::getModel('core/input_filter');
        $filter->setFilters($this->_filters);
        $this->setData($filter->filter($data));
        return $this;
    }
}
