<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Acl role user grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 *
 * @method Mage_Api_Model_Resource_Roles_User_Collection getCollection()
 */
class Mage_Adminhtml_Block_Api_Role_Grid_User extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('role_user_id');
        $this->setDefaultDir('asc');
        $this->setId('roleUserGrid');
        $this->setDefaultFilter(['in_role_users' => 1]);
        $this->setUseAjax(true);
    }

    /**
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return $this
     * @throws Exception
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() === 'in_role_users') {
            $inRoleIds = $this->_getUsers();
            if (empty($inRoleIds)) {
                $inRoleIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('user_id', ['in' => $inRoleIds]);
            } elseif ($inRoleIds) {
                $this->getCollection()->addFieldToFilter('user_id', ['nin' => $inRoleIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @inheritDoc
     * @throws Mage_Core_Exception
     */
    protected function _prepareCollection()
    {
        $roleId = $this->getRequest()->getParam('rid');
        Mage::register('RID', $roleId);
        $collection = Mage::getModel('api/roles')->getUsersCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_role_users', [
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_role_users',
            'values'    => $this->_getUsers(),
            'align'     => 'center',
            'index'     => 'user_id'
        ]);

        $this->addColumn('role_user_id', [
            'header'    => Mage::helper('adminhtml')->__('User ID'),
            'width'     => 5,
            'align'     => 'left',
            'index'     => 'user_id'
        ]);

        $this->addColumn('role_user_username', [
            'header'    => Mage::helper('adminhtml')->__('User Name'),
            'align'     => 'left',
            'index'     => 'username'
        ]);

        $this->addColumn('role_user_firstname', [
            'header'    => Mage::helper('adminhtml')->__('First Name'),
            'align'     => 'left',
            'index'     => 'firstname'
        ]);

        $this->addColumn('role_user_lastname', [
            'header'    => Mage::helper('adminhtml')->__('Last Name'),
            'align'     => 'left',
            'index'     => 'lastname'
        ]);

        $this->addColumn('role_user_email', [
            'header'    => Mage::helper('adminhtml')->__('Email'),
            'width'     => 40,
            'align'     => 'left',
            'index'     => 'email'
        ]);

        $this->addColumn('role_user_is_active', [
            'header'    => Mage::helper('adminhtml')->__('Status'),
            'index'     => 'is_active',
            'align'     => 'left',
            'type'      => 'options',
            'options'   => ['1' => Mage::helper('adminhtml')->__('Active'), '0' => Mage::helper('adminhtml')->__('Inactive')],
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getGridUrl()
    {
        $roleId = $this->getRequest()->getParam('rid');
        return $this->getUrl('*/*/editrolegrid', ['rid' => $roleId]);
    }

    /**
     * @param bool $json
     * @return array|int|string
     * @throws Exception
     */
    protected function _getUsers($json = false)
    {
        if ($this->getRequest()->getParam('in_role_user') != '') {
            return (int)$this->getRequest()->getParam('in_role_user');
        }
        $roleId = ($this->getRequest()->getParam('rid') > 0) ? $this->getRequest()->getParam('rid') : Mage::registry('RID');
        $users  = Mage::getModel('api/roles')->setId($roleId)->getRoleUsers();
        if (count($users)) {
            if ($json) {
                $jsonUsers = [];
                foreach ($users as $usrid) {
                    $jsonUsers[$usrid] = 0;
                }
                return Mage::helper('core')->jsonEncode((object)$jsonUsers);
            }

            return array_values($users);
        }

        if ($json) {
            return '{}';
        }

        return [];
    }
}
