<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Admin page left menu
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Api_User_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('adminhtml')->__('User Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main_section', [
            'label'     => Mage::helper('adminhtml')->__('User Info'),
            'title'     => Mage::helper('adminhtml')->__('User Info'),
            'content'   => $this->getLayout()->createBlock('adminhtml/api_user_edit_tab_main')->toHtml(),
            'active'    => true,
        ]);

        $this->addTab('roles_section', [
            'label'     => Mage::helper('adminhtml')->__('User Role'),
            'title'     => Mage::helper('adminhtml')->__('User Role'),
            'content'   => $this->getLayout()->createBlock('adminhtml/api_user_edit_tab_roles', 'user.roles.grid')->toHtml(),
        ]);
        return parent::_beforeToHtml();
    }
}
