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
 * API2 role list for admin user permissions
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Block_Adminhtml_Permissions_User_Edit_Tab_Roles extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Selected API2 roles for grid
     *
     * @var array
     */
    protected $_selectedRoles;

    public function __construct()
    {
        parent::__construct();

        $this->setId('api2_roles_section')
            ->setDefaultSort('sort_order')
            ->setDefaultDir(Varien_Db_Select::SQL_ASC)
            ->setTitle($this->__('REST Roles Information'))
            ->setUseAjax(true);
    }

    /**
     * Prepare grid collection object
     *
     * @inheritDoc
     */
    protected function _prepareCollection()
    {
        /** @var Mage_Api2_Model_Resource_Acl_Global_Role_Collection $collection */
        $collection = Mage::getResourceModel('api2/acl_global_role_collection');
        $collection->addFieldToFilter('entity_id', ['nin' => Mage_Api2_Model_Acl_Global_Role::getSystemRoles()]);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @inheritDoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('assigned_user_role', [
            'header_css_class' => 'a-center',
            'header'    => $this->__('Assigned'),
            'type'      => 'radio',
            'html_name' => 'api2_roles[]',
            'values'    => $this->_getSelectedRoles(),
            'align'     => 'center',
            'index'     => 'entity_id',
        ]);

        $this->addColumn('role_name', [
            'header'    => $this->__('Role Name'),
            'index'     => 'role_name',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Add custom column filter to collection
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'assigned_user_role') {
            $userRoles = $this->_getSelectedRoles();
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $userRoles]);
            } elseif (!empty($userRoles)) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $userRoles]);
            } else {
                $this->getCollection();
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * Get selected API2 roles for grid
     *
     * @return array
     */
    protected function _getSelectedRoles()
    {
        if ($this->_selectedRoles === null) {
            $userRoles = [];

            /** @var Mage_Admin_Model_User $user */
            $user = Mage::registry('permissions_user');
            if ($user->getId()) {
                /** @var Mage_Api2_Model_Resource_Acl_Global_Role_Collection $collection */
                $collection = Mage::getResourceModel('api2/acl_global_role_collection');
                $collection->addFilterByAdminId($user->getId());

                $userRoles = $collection->getAllIds();
            }

            $this->_selectedRoles = $userRoles;
        }

        return $this->_selectedRoles;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('REST Role');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('REST Role');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return false
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Get controller action url for grid ajax actions
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/api2_role/rolesGrid',
            ['user_id' => Mage::registry('permissions_user')->getUserId()],
        );
    }
}
