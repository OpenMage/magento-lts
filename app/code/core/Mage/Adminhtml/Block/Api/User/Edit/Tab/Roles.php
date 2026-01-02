<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Class Mage_Adminhtml_Block_Api_User_Edit_Tab_Roles
 *
 * @package    Mage_Adminhtml
 *
 * @method Mage_Api_Model_Resource_Role_Collection getCollection()
 */
class Mage_Adminhtml_Block_Api_User_Edit_Tab_Roles extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('permissionsUserRolesGrid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('asc');
        //$this->setDefaultFilter(array('assigned_user_role'=>1));
        $this->setTitle(Mage::helper('adminhtml')->__('User Roles Information'));
        $this->setUseAjax(true);
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() === 'assigned_user_role') {
            $userRoles = $this->_getSelectedRoles();
            if (empty($userRoles)) {
                $userRoles = 0;
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('role_id', ['in' => $userRoles]);
            } elseif ($userRoles) {
                $this->getCollection()->addFieldToFilter('role_id', ['nin' => $userRoles]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('api/role_collection');
        $collection->setRolesFilter();
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
            'header'    => Mage::helper('adminhtml')->__('Assigned'),
            'type'      => 'radio',
            'html_name' => 'roles[]',
            'values'    => $this->_getSelectedRoles(),
            'align'     => 'center',
            'index'     => 'role_id',
        ]);

        $this->addColumn('role_name', [
            'header'    => Mage::helper('adminhtml')->__('Role Name'),
            'index'     => 'role_name',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/rolesGrid', ['user_id' => Mage::registry('api_user')->getUserId()]);
    }

    /**
     * @param  bool      $json
     * @return string
     * @throws Exception
     */
    protected function _getSelectedRoles($json = false)
    {
        if ($this->getRequest()->getParam('user_roles') != '') {
            return $this->getRequest()->getParam('user_roles');
        }

        $uRoles = Mage::registry('api_user')->getRoles();
        if ($json) {
            $jsonRoles = [];
            foreach ($uRoles as $urid) {
                $jsonRoles[$urid] = 0;
            }

            return Mage::helper('core')->jsonEncode((object) $jsonRoles);
        }

        return $uRoles;
    }
}
