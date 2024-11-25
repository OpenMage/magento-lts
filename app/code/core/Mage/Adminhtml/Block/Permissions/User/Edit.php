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
 * Adminhtml permissions user edit page
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_User_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'user_id';
        $this->_controller = 'permissions_user';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save User'));
        $this->_updateButton('delete', 'label', Mage::helper('adminhtml')->__('Delete User'));
        $this->_updateButton('delete', 'onclick', 'if(confirm(\'' . Mage::helper('core')->jsQuoteEscape(
            Mage::helper('adminhtml')->__('Are you sure you want to do this?'),
        ) . '\')) editForm.submit(\'' . $this->getUrl('*/*/delete') . '\'); return false;');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('permissions_user')->getId()) {
            return Mage::helper('adminhtml')->__("Edit User '%s'", $this->escapeHtml(Mage::registry('permissions_user')->getUsername()));
        }
        return Mage::helper('adminhtml')->__('New User');
    }
}
