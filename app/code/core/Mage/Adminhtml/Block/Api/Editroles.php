<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Adminhtml_Block_Api_Editroles extends Mage_Adminhtml_Block_Widget_Tabs {
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
        $role = Mage::getModel("api/roles")
           ->load($roleId);

        $this->addTab('info', array(
            'label'     => Mage::helper('adminhtml')->__('Role Info'),
            'title'     => Mage::helper('adminhtml')->__('Role Info'),
            'content'   => $this->getLayout()->createBlock('adminhtml/api_tab_roleinfo')->setRole($role)->toHtml(),
            'active'    => true
        ));

        $this->addTab('account', array(
            'label'     => Mage::helper('adminhtml')->__('Role Resources'),
            'title'     => Mage::helper('adminhtml')->__('Role Resources'),
            'content'   => $this->getLayout()->createBlock('adminhtml/api_tab_rolesedit')->toHtml(),
        ));

        if( intval($roleId) > 0 ) {
            $this->addTab('roles', array(
                'label'     => Mage::helper('adminhtml')->__('Role Users'),
                'title'     => Mage::helper('adminhtml')->__('Role Users'),
                'content'   => $this->getLayout()->createBlock('adminhtml/api_tab_rolesusers', 'role.users.grid')->toHtml(),
            ));
        }
        return parent::_beforeToHtml();
    }
}
