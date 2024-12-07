<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Block for rendering buttons
 *
 * @category   Mage
 * @package    Mage_Api2
 *
 * @method Mage_Api2_Model_Acl_Global_Role getRole()
 * @method $this setRole(Mage_Api2_Model_Acl_Global_Role $role)
 */
class Mage_Api2_Block_Adminhtml_Roles_Buttons extends Mage_Adminhtml_Block_Template
{
    public const BUTTON_BACK    = 'backButton';
    public const BUTTON_DELETE  = 'deleteButton';
    public const BUTTON_RESET   = 'resetButton';
    public const BUTTON_SAVE    = 'saveButton';

    protected $_template = 'api2/role/buttons.phtml';

    /**
     * @codeCoverageIgnore
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->addButtons();
        return parent::_prepareLayout();
    }

    /**
     * @codeCoverageIgnore
     */
    protected function addButtons(): void
    {
        $this->setChild(self::BUTTON_BACK, $this->getButtonBackBlock());
        $this->setChild(self::BUTTON_RESET, $this->getButtonResetBlock());
        $this->setChild(self::BUTTON_SAVE, $this->getButtonSaveBlock());
        $this->setChild(self::BUTTON_DELETE, $this->getButtonDeleteBlock());
    }

    public function getButtonBackBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlockByType(self::BUTTON_BACK);
    }

    public function getButtonDeleteBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlockByType(self::BUTTON_DELETE)
            ->setLabel(Mage::helper('adminhtml')->__('Delete Role'))
            ->setOnClick('window.location.reload()');
    }

    public function getButtonResetBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlockByType(self::BUTTON_RESET)
            ->setOnClick('window.location.reload()')
            ->resetClass();
    }

    public function getButtonSaveBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlockByType(self::BUTTON_SAVE)
            ->setLabel(Mage::helper('adminhtml')->__('Save Role'))
            ->setOnClick('roleForm.submit(); return false;');
    }

    /**
     * @inheritDoc
     */
    public function getDeleteButtonHtml()
    {
        if (!$this->getRole() || !$this->getRole()->getId()
            || Mage_Api2_Model_Acl_Global_Role::isSystemRole($this->getRole())
        ) {
            return '';
        }

        $this->getChild(self::BUTTON_DELETE)->setData('onclick', sprintf(
            "if(confirm('%s')) roleForm.submit('%s'); return false;",
            Mage::helper('core')->jsQuoteEscape(Mage::helper('adminhtml')->__('Are you sure you want to do this?')),
            $this->getUrl('*/*/delete')
        ));

        return parent::getDeleteButtonHtml();
    }

    /**
     * Get block caption
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->getRole() && $this->getRole()->getId()
                ? ($this->__('Edit Role') . " '{$this->escapeHtml($this->getRole()->getRoleName())}'")
                : $this->__('Add New Role');
    }
}
