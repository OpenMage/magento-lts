<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_Tab_Rolesusers extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();

        $roleId = $this->getRequest()->getParam('rid', false);

        $users = Mage::getModel('admin/user')->getCollection()->load();
        $this->setTemplate('permissions/rolesusers.phtml')
            ->assign('users', $users->getItems())
            ->assign('roleId', $roleId);
    }

    protected function _prepareLayout()
    {
        $this->setChild('userGrid', $this->getLayout()->createBlock('adminhtml/permissions_role_grid_user', 'roleUsersGrid'));
        return parent::_prepareLayout();
    }

    protected function _getGridHtml()
    {
        return $this->getChildHtml('userGrid');
    }

    protected function _getJsObjectName()
    {
        return $this->getChild('userGrid')->getJsObjectName();
    }
}
