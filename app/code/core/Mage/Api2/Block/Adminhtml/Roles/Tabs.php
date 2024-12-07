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
 * Block tabs for role edit page
 *
 * @category   Mage
 * @package    Mage_Api2
 *
 * @method Mage_Api2_Model_Acl_Global_Role getRole()
 * @method $this setRole(Mage_Api2_Model_Acl_Global_Role $role)
 */
class Mage_Api2_Block_Adminhtml_Roles_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('role_info_tabs');
        $this->setDestElementId('role_edit_form');
        $this->setData('title', Mage::helper('api2')->__('Role Information'));
    }

    /**
     * Hook before html rendering
     *
     * @inheritDoc
     */
    protected function _beforeToHtml()
    {
        $role = $this->getRole();
        if ($role && Mage_Api2_Model_Acl_Global_Role::isSystemRole($role)) {
            $this->setActiveTab('api2_role_section_resources');
        } else {
            $this->setActiveTab('api2_role_section_info');
        }
        return parent::_beforeToHtml();
    }
}
