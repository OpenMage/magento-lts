<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_Editroles extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('role_info_tabs');
        $this->setDestElementId('role_edit_form');
        $this->setTitle(Mage::helper('adminhtml')->__('Role Information'));
    }

    protected function _prepareLayout()
    {
        $role = Mage::registry('current_role');

        $this->addTab('info', $this->getLayout()->createBlock('adminhtml/permissions_tab_roleinfo')->setRole($role)->setActive(true));
        $this->addTab('account', $this->getLayout()->createBlock('adminhtml/permissions_tab_rolesedit', 'adminhtml.permissions.tab.rolesedit'));

        if ($role->getId()) {
            $this->addTab('roles', [
                'label'     => Mage::helper('adminhtml')->__('Role Users'),
                'title'     => Mage::helper('adminhtml')->__('Role Users'),
                'content'   => $this->getLayout()->createBlock('adminhtml/permissions_tab_rolesusers', 'role.users.grid')->toHtml(),
            ]);
        }

        return parent::_prepareLayout();
    }
}
