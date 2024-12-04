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
 * Manage currency block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Currency extends Mage_Adminhtml_Block_Template
{
    public const BLOCK_IMPORT_SERVICES  = 'import_services';
    public const BLOCK_RATE_MATRIX      = 'rates_matrix';

    public const BUTTON_IMPORT          = 'import_button';

    protected $_template = 'system/currency/rates.phtml';

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            self::BLOCK_RATE_MATRIX,
            $this->getLayout()->createBlock('adminhtml/system_currency_rate_matrix')
        );

        $this->setChild(
            self::BLOCK_IMPORT_SERVICES,
            $this->getLayout()->createBlock('adminhtml/system_currency_rate_services')
        );

        $this->addButtons();
        return parent::_prepareLayout();
    }

    /**
     * @codeCoverageIgnore
     */
    protected function addButtons(): void
    {
        $this->setChild(self::BUTTON_SAVE, $this->getButtonSaveBlock());
        $this->setChild(self::BUTTON_RESET, $this->getButtonResetBlock());
        $this->setChild(self::BUTTON_IMPORT, $this->getButtonImportBlock());
    }

    public function getButtonImportBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonAddBlock($name, $attributes)
            ->setLabel(Mage::helper('adminhtml')->__('Import'))
            ->setType(Mage_Adminhtml_Block_Widget_Button::TYPE_SUBMIT);
    }

    public function getButtonResetBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonResetBlock($name, $attributes)
            ->setOnClick('document.location.reload()');
    }

    public function getButtonSaveBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonSaveBlock($name, $attributes)
            ->setLabel(Mage::helper('adminhtml')->__('Save Currency Rates'))
            ->setOnClick('currencyForm.submit();');
    }

    protected function getHeader()
    {
        return Mage::helper('adminhtml')->__('Manage Currency Rates');
    }

    protected function getImportButtonHtml()
    {
        return $this->getChildHtml(self::BUTTON_IMPORT);
    }

    protected function getServicesHtml()
    {
        return $this->getChildHtml(self::BLOCK_IMPORT_SERVICES);
    }

    protected function getRatesMatrixHtml()
    {
        return $this->getChildHtml(self::BLOCK_RATE_MATRIX);
    }

    protected function getImportFormAction()
    {
        return $this->getUrl('*/*/fetchRates');
    }
}
