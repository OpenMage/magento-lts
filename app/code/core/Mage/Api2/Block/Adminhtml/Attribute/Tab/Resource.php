<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Block for rendering attributes tree list tab
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Api2_Model_Acl_Global_Role getRole()
 * @method $this setRole(Mage_Api2_Model_Acl_Global_Role $role)
 */
class Mage_Api2_Block_Adminhtml_Attribute_Tab_Resource extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * @var Mage_Api2_Model_Acl_Global_Rule_Tree
     */
    protected $_treeModel = false;

    public function __construct()
    {
        parent::__construct();

        $this->setId('api2_attribute_section_resources')
                ->setData('default_dir', Varien_Db_Select::SQL_ASC)
                ->setData('default_sort', 'sort_order')
                ->setData('title', $this->__('Attribute Rules Information'))
                ->setData('use_ajax', true);

        $this->_treeModel = Mage::getModel(
            'api2/acl_global_rule_tree',
            ['type' => Mage_Api2_Model_Acl_Global_Rule_Tree::TYPE_ATTRIBUTE]
        );

        /** @var Mage_Api2_Model_Acl_Filter_Attribute_ResourcePermission $permissions */
        $permissions = Mage::getModel('api2/acl_filter_attribute_resourcePermission');
        $permissions->setFilterValue($this->getRequest()->getParam('type'));
        $this->_treeModel->setResourcesPermissions($permissions->getResourcesPermissions())
            ->setHasEntityOnlyAttributes($permissions->getHasEntityOnlyAttributes());
    }

    /**
     * Get Json Representation of Resource Tree
     *
     * @return string
     */
    public function getResTreeJson()
    {
        /** @var Mage_Core_Helper_Data $helper */
        $helper = Mage::helper('core');
        return $helper->jsonEncode($this->_treeModel->getTreeResources());
    }

    /**
     * Check if everything is allowed
     *
     * @return bool
     */
    public function getEverythingAllowed()
    {
        return $this->_treeModel->getEverythingAllowed();
    }

    /**
     * Check if tree has entity only attributes
     *
     * @return bool
     */
    public function hasEntityOnlyAttributes()
    {
        return $this->_treeModel->getHasEntityOnlyAttributes();
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('ACL Attribute Rules');
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
