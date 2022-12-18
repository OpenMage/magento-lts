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
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Block for rendering users list tab
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Admin_Model_Resource_User_Collection getCollection()
 * @method Mage_Api2_Model_Acl_Global_Role getRole()
 * @method $this setRole(Mage_Api2_Model_Acl_Global_Role $role)
 * @method $this setUsers(array $users)
 */
class Mage_Api2_Block_Adminhtml_Roles_Tab_Users extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('roleUsersGrid');
        $this->setData('use_ajax', true);
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('user_id')
            ->setDefaultDir(Varien_Db_Select::SQL_DESC);
        $this->setDefaultFilter(['filter_in_role_users' => 1]);
    }

    /**
     * Prepare collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        /** @var Mage_Admin_Model_Resource_User_Collection $collection */
        $collection = Mage::getModel('admin/user')->getCollection();
        $collection->getSelect()->joinLeft(
            ['acl' => $collection->getTable('api2/acl_user')],
            'acl.admin_id = main_table.user_id',
            'role_id'
        );
        if ($this->getRole() && $this->getRole()->getId()) {
            $collection->addFilter('acl.role_id', $this->getRole()->getId());
        }

        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare columns
     *
     * @inheritDoc
     */
    protected function _prepareColumns()
    {
        $this->addColumn('filter_in_role_users', [
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'filter_in_role_users',
            'values'    => $this->getUsers(),
            'align'     => 'center',
            'index'     => 'user_id'
        ]);

        $this->addColumn('user_id', [
            'header' => Mage::helper('api2')->__('ID'), 'index' => 'user_id', 'align' => 'right', 'width' => '50px',
        ]);

        $this->addColumn('username', [
            'header' => Mage::helper('adminhtml')->__('User Name'), 'align' => 'left', 'index' => 'username'
        ]);

        $this->addColumn('firstname', [
            'header' => Mage::helper('adminhtml')->__('First Name'), 'align' => 'left', 'index' => 'firstname'
        ]);

        $this->addColumn('lastname', [
            'header' => Mage::helper('adminhtml')->__('Last Name'), 'align' => 'left', 'index' => 'lastname'
        ]);

        return parent::_prepareColumns();
    }

    /**
     * Get grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/usersGrid', ['_current' => true]);
    }

    /**
     * Get row URL
     *
     * @param Mage_Api2_Model_Acl_Global_Role $row
     * @return string|null
     */
    public function getRowUrl($row)
    {
        return null;
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('api2')->__('Role Users');
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
        return !$this->isHidden();
    }

    /**
     * Whether tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return $this->getRole() && Mage_Api2_Model_Acl_Global_Role::isSystemRole($this->getRole());
    }

    /**
     * Render block only when not hidden
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->isHidden()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'filter_in_role_users') {
            $inRoleIds = $this->getUsers();
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

    /**
     * Get users
     *
     * @param bool $json
     * @return array|string
     */
    public function getUsers($json = false)
    {
        $users = $this->getData('users');

        if ($json) {
            if ($users === []) {
                return '{}';
            }
            $jsonUsers = [];
            foreach ($users as $usrId) {
                $jsonUsers[$usrId] = 0;
            }
            /** @var Mage_Core_Helper_Data $helper */
            $helper = Mage::helper('core');
            $result = $helper->jsonEncode((object) $jsonUsers);
        } else {
            $result = array_values($users);
        }

        return $result;
    }
}
