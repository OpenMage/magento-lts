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
class Mage_Adminhtml_Block_Api_Editroles extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('role_info_tabs');
        $this->setDestElementId('role_edit_form');
        $this->setTitle(Mage::helper('adminhtml')->__('Role Information'));
    }

    protected function _beforeToHtml()
    {
        $roleId = $this->getRequest()->getParam('rid', false);
        $role = Mage::getModel('api/roles')
           ->load($roleId);

        $this->addTab('info', [
            'label'     => Mage::helper('adminhtml')->__('Role Info'),
            'title'     => Mage::helper('adminhtml')->__('Role Info'),
            'content'   => $this->getLayout()->createBlock('adminhtml/api_tab_roleinfo')->setRole($role)->toHtml(),
            'active'    => true,
        ]);

        $this->addTab('account', [
            'label'     => Mage::helper('adminhtml')->__('Role Resources'),
            'title'     => Mage::helper('adminhtml')->__('Role Resources'),
            'content'   => $this->getLayout()->createBlock('adminhtml/api_tab_rolesedit')->toHtml(),
        ]);

        if ((int) $roleId > 0) {
            $this->addTab('roles', [
                'label'     => Mage::helper('adminhtml')->__('Role Users'),
                'title'     => Mage::helper('adminhtml')->__('Role Users'),
                'content'   => $this->getLayout()->createBlock('adminhtml/api_tab_rolesusers', 'role.users.grid')->toHtml(),
            ]);
        }

        return parent::_beforeToHtml();
    }
}
