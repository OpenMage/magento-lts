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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Adminhtml_Block_Permissions_Edituser extends Mage_Adminhtml_Block_Widget_Tabs {
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_info_tabs');
        $this->setDestElementId('user_edit_form');
    }

    protected function _beforeToHtml()
    {
        $this->addTab('account', array(
            'label'     => Mage::helper('adminhtml')->__('User Info'),
            'title'     => Mage::helper('adminhtml')->__('User Info'),
            'content'   => $this->getLayout()->createBlock('adminhtml/permissions_tab_useredit')->toHtml(),
            'active'    => true
        ));
        if( $this->getUser()->getUserId() ) {
            $this->addTab('roles', array(
                'label'     => Mage::helper('adminhtml')->__('Roles'),
                'title'     => Mage::helper('adminhtml')->__('Roles'),
                'content'   => $this->getLayout()->createBlock('adminhtml/permissions_tab_userroles')->toHtml(),
            ));
        }
        return parent::_beforeToHtml();
    }

    public function getUser()
    {
        return Mage::registry('user_data');
    }
}
