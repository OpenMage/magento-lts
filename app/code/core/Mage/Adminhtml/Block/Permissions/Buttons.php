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
 * @copyright  Copyright (c) 2017-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_Buttons extends Mage_Adminhtml_Block_Template
{
    public const BUTTON_BACK    = 'backButton';
    public const BUTTON_DELETE  = 'deleteButton';
    public const BUTTON_RESET   = 'resetButton';
    public const BUTTON_SAVE    = 'saveButton';

    protected $_template = 'permissions/userinfo.phtml';

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

    public function getButtonBackBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBackBlock($name, $attributes)
            ->setOnClick('window.location.href=\'' . $this->getUrl('*/*/') . '\'');
    }

    public function getButtonDeleteBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonDeleteBlock($name, $attributes)
            ->setLabel(Mage::helper('adminhtml')->__('Delete Role'))
            ->setOnClick('if(confirm(\'' . Mage::helper('core')->jsQuoteEscape(
                Mage::helper('adminhtml')->__('Are you sure you want to do this?')
            ) . '\')) roleForm.submit(\'' . $this->getUrl('*/*/delete') . '\'); return false;');
    }

    public function getButtonResetBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonResetBlock($name, $attributes)
            ->setOnClick('window.location.reload()')
            ->resetClass();
    }

    public function getButtonSaveBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonSaveBlock($name, $attributes)
            ->setLabel(Mage::helper('adminhtml')->__('Save Role'))
            ->setOnClick('roleForm.submit();return false;');
    }

    /**
     * @inheritDoc
     */
    public function getDeleteButtonHtml()
    {
        if ((int) $this->getRequest()->getParam('rid') == 0) {
            return '';
        }
        return parent::getDeleteButtonHtml();
    }

    public function getUser()
    {
        return Mage::registry('user_data');
    }
}
