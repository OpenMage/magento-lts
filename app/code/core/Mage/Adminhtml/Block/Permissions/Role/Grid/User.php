<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Acl role user grid
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_Role_Grid_User extends Mage_Adminhtml_Block_Widget_Grid
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

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_role_users') {
            $inRoleIds = $this->_getUsers();
            if (empty($inRoleIds)) {
                $inRoleIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('user_id', ['in' => $inRoleIds]);
            } else {
                if ($inRoleIds) {
                    $this->getCollection()->addFieldToFilter('user_id', ['nin' => $inRoleIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        $roleId = $this->getRequest()->getParam('rid');
        Mage::register('RID', $roleId);
        $collection = Mage::getModel('admin/roles')->getUsersCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('in_role_users', [
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_role_users',
            'values'    => $this->_getUsers(),
            'align'     => 'center',
            'index'     => 'user_id',
        ]);

        $this->addColumn('role_user_id', [
            'header'    => Mage::helper('adminhtml')->__('User ID'),
            'width'     => 5,
            'align'     => 'left',
            'index'     => 'user_id',
        ]);

        $this->addColumn('role_user_username', [
            'header'    => Mage::helper('adminhtml')->__('User Name'),
            'align'     => 'left',
            'index'     => 'username',
        ]);

        $this->addColumn('role_user_firstname', [
            'header'    => Mage::helper('adminhtml')->__('First Name'),
            'align'     => 'left',
            'index'     => 'firstname',
        ]);

        $this->addColumn('role_user_lastname', [
            'header'    => Mage::helper('adminhtml')->__('Last Name'),
            'align'     => 'left',
            'index'     => 'lastname',
        ]);

        $this->addColumn('role_user_email', [
            'header'    => Mage::helper('adminhtml')->__('Email'),
            'width'     => 40,
            'align'     => 'left',
            'index'     => 'email',
        ]);

        $this->addColumn('role_user_is_active', [
            'header'    => Mage::helper('adminhtml')->__('Status'),
            'index'     => 'is_active',
            'align'     => 'left',
            'type'      => 'options',
            'options'   => ['1' => Mage::helper('adminhtml')->__('Active'), '0' => Mage::helper('adminhtml')->__('Inactive')],
        ]);

        /*
         $this->addColumn('grid_actions',
             array(
                 'header'=>Mage::helper('adminhtml')->__('Actions'),
                 'width'=>5,
                 'sortable'=>false,
                 'filter'    =>false,
                 'type' => 'action',
                 'actions'   => array(
                                     array(
                                         'caption' => Mage::helper('adminhtml')->__('Remove'),
                                         'onClick' => 'role.deleteFromRole($role_id);'
                                     )
                                 )
             )
         );
         */

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        $roleId = $this->getRequest()->getParam('rid');
        return $this->getUrl('*/*/editrolegrid', ['rid' => $roleId]);
    }

    protected function _getUsers($json = false)
    {
        if ($this->getRequest()->getParam('in_role_user') != '') {
            return $this->getRequest()->getParam('in_role_user');
        }
        $roleId = ($this->getRequest()->getParam('rid') > 0) ? $this->getRequest()->getParam('rid') : Mage::registry('RID');
        $users  = Mage::getModel('admin/roles')->setId($roleId)->getRoleUsers();
        if (count($users)) {
            if ($json) {
                $jsonUsers = [];
                foreach ($users as $usrid) {
                    $jsonUsers[$usrid] = 0;
                }
                return Mage::helper('core')->jsonEncode((object) $jsonUsers);
            } else {
                return array_values($users);
            }
        } else {
            if ($json) {
                return '{}';
            } else {
                return [];
            }
        }
    }
}
