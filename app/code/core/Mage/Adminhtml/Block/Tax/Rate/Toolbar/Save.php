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
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Admin tax rate save toolbar
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tax_Rate_Toolbar_Save extends Mage_Adminhtml_Block_Template
{
    public const BUTTON_BACK    = 'backButton';
    public const BUTTON_DELETE  = 'deleteButton';
    public const BUTTON_RESET   = 'resetButton';
    public const BUTTON_SAVE    = 'saveButton';

    protected $_template = 'tax/toolbar/rate/save.phtml';

    /**
     * Mage_Adminhtml_Block_Tax_Rate_Toolbar_Save constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->assign('createUrl', $this->getUrl('*/tax_rate/save'));
    }

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

    public function getButtonDeleteBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonDeleteBlock($name, $attributes)
            ->setLabel(Mage::helper('tax')->__('Delete Rate'))
            ->setOnClickSetLocationJsUrl('*/*/delete', ['rate' => $this->getRequest()->getParam('rate')]);
    }

    public function getButtonResetBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonResetBlock($name, $attributes)
            ->setOnClick('window.location.reload()');
    }

    public function getButtonSaveBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonSaveBlock($name, $attributes)
            ->setLabel(Mage::helper('tax')->__('Save Rate'))
            ->setOnClick('wigetForm.submit();return false;');
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getDeleteButtonHtml()
    {
        if ((int) $this->getRequest()->getParam('rate') == 0) {
            return '';
        }
        return parent::getDeleteButtonHtml();
    }
}
