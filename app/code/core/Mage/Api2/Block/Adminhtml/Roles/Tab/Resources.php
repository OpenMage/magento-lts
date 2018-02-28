<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Block for rendering resources list tab
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 * @method Mage_Api2_Model_Acl_Global_Role getRole()
 * @method Mage_Api2_Block_Adminhtml_Roles_Tab_Resources setRole(Mage_Api2_Model_Acl_Global_Role $role)
 */
class Mage_Api2_Block_Adminhtml_Roles_Tab_Resources extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Role model
     *
     * @var Mage_Api2_Model_Acl_Global_Role
     */
    protected $_role;

    /**
     * Tree model
     *
     * @var Mage_Api2_Model_Acl_Global_Rule_Tree
     */
    protected $_treeModel = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('api2_role_section_resources')
                ->setData('default_dir', Varien_Db_Select::SQL_ASC)
                ->setData('default_sort', 'sort_order')
                ->setData('title', Mage::helper('api2')->__('Api Rules Information'))
                ->setData('use_ajax', true);

        $this->_treeModel = Mage::getModel(
            'api2/acl_global_rule_tree', array('type' => Mage_Api2_Model_Acl_Global_Rule_Tree::TYPE_PRIVILEGE)
        );
    }

    /**
     * Get Json Representation of Resource Tree
     *
     * @return string
     */
    public function getResTreeJson()
    {
        $this->_prepareTreeModel();
        /** @var $helper Mage_Core_Helper_Data */
        $helper = Mage::helper('core');
        return $helper->jsonEncode($this->_treeModel->getTreeResources());
    }

    /**
     * Prepare tree model
     *
     * @return Mage_Api2_Block_Adminhtml_Roles_Tab_Resources
     */
    public function _prepareTreeModel()
    {
        $role = $this->getRole();
        if ($role) {
            $permissionModel = $role->getPermissionModel();
            $permissionModel->setFilterValue($role);
            $this->_treeModel->setResourcesPermissions($permissionModel->getResourcesPermissions());
        } else {
            $role = Mage::getModel('api2/acl_global_role');
        }
        $this->_treeModel->setRole($role);
        return $this;
    }

    /**
     * Check if everything is allowed
     *
     * @return boolean
     */
    public function getEverythingAllowed()
    {
        $this->_prepareTreeModel();
        return $this->_treeModel->getEverythingAllowed();
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('api2')->__('Role API Resources');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Whether tab is available
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Whether tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
