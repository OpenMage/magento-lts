<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * @package    Mage_Api
 *
 * @method Mage_Api_Model_Resource_Roles _getResource()
 * @method Mage_Api_Model_Resource_Roles_Collection getCollection()
 * @method string getName()
 * @method int getParentId()
 * @method int getPid()
 * @method Mage_Api_Model_Resource_Roles getResource()
 * @method Mage_Api_Model_Resource_Roles_Collection getResourceCollection()
 * @method string getRoleName()
 * @method string getRoleType()
 * @method int getSortOrder()
 * @method int getTreeLevel()
 * @method int getUserId()
 * @method $this setName(string $name)
 * @method $this setParentId(int $value)
 * @method $this setRoleName(string $value)
 * @method $this setRoleType(string $value)
 * @method $this setSortOrder(int $value)
 * @method $this setTreeLevel(int $value)
 * @method $this setUserId(int $value)
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
     * @param null|string $parentName
     * @param int $level
     * @param null|bool $represent2Darray
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
            if (!in_array($resource->getName(), ['title', 'sort_order', 'children'])
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
