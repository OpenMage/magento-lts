<?php

use Laminas\Db\Sql\Select;

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */
/**
 * API2 role list for admin user permissions
 *
 * @package    Mage_Api2
 *
 * @method Varien_Data_Collection_Db getCollection()
 */
class Mage_Api2_Block_Adminhtml_Permissions_User_Edit_Tab_Roles extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected string $_eventPrefix = 'api2_adminhtml_permissions_user_edit_tab_roles';

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
            ->setDefaultDir(Select::ORDER_ASCENDING)
            ->setTitle($this->__('REST Roles Information'))
            ->setUseAjax(true);
    }

    /**
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
     * @inheritDoc
     * @throws Exception
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
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getTabLabel()
    {
        return $this->__('REST Role');
    }

    /**
     * @inheritDoc
     */
    public function getTabTitle()
    {
        return $this->__('REST Role');
    }

    /**
     * @inheritDoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/api2_role/rolesGrid',
            ['user_id' => Mage::registry('permissions_user')->getUserId()],
        );
    }
}
