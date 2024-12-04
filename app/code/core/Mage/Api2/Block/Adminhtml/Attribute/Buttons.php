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
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Block for rendering buttons
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Block_Adminhtml_Attribute_Buttons extends Mage_Adminhtml_Block_Template
{
    public const BUTTON_BACK    = 'backButton';
    public const BUTTON_SAVE    = 'saveButton';

    protected $_template = 'api2/attribute/buttons.phtml';

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
        $this->setChild(self::BUTTON_SAVE, $this->getButtonSaveBlock());
    }

    public function getButtonBackBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBackBlock($name, $attributes)
            ->setOnClick(sprintf("window.location.href='%s';", $this->getUrl('*/*/')));
    }

    public function getButtonSaveBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonSaveBlock($name, $attributes)
            ->setOnClick('form.submit(); return false;');
    }

    /**
     * Get block caption
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->__('Edit');
    }
}
