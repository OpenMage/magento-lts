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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin page left menu
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
            'active'    => true
        ]);

        $this->addTab('roles_section', [
            'label'     => Mage::helper('adminhtml')->__('User Role'),
            'title'     => Mage::helper('adminhtml')->__('User Role'),
            'content'   => $this->getLayout()->createBlock('adminhtml/api_user_edit_tab_roles', 'user.roles.grid')->toHtml(),
        ]);
        return parent::_beforeToHtml();
    }
}
