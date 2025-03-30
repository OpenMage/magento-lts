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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
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
