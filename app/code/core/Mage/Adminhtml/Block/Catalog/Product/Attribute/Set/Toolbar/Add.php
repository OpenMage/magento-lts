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
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Toolbar_Add extends Mage_Adminhtml_Block_Template
{
    public const BLOCK_FORM = 'setForm';

    protected $_template = 'catalog/product/attribute/set/toolbar/add.phtml';

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            self::BLOCK_FORM,
            $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_set_main_formset')
        );

        $this->addButtons();
        return parent::_prepareLayout();
    }

    /**
     * @codeCoverageIgnore
     */
    protected function addButtons(): void
    {
        $this->setChild(self::BUTTON_SAVE, $this->getButtonSaveGroupBlock());
        $this->setChild(self::BUTTON_BACK, $this->getButtonBackBlock());
    }

    public function getButtonBackBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBackBlock($name, $attributes)
            ->setOnClickSetLocationJsFullUrl($this->getUrl('*/*/'));
    }

    public function getButtonSaveGroupBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonSaveBlock()
            ->setLabel(Mage::helper('catalog')->__('Save Attribute Set'))
            ->setOnClick('if (addSet.submit()) disableElements(\'save\');')
            ->resetClass();
    }

    /**
     * @return string
     */
    protected function _getHeader()
    {
        return Mage::helper('catalog')->__('Add New Attribute Set');
    }

    /**
     * @return string
     */
    protected function getFormHtml()
    {
        return $this->getChildHtml(self::BLOCK_FORM);
    }

    protected function getFormId()
    {
        return $this->getChild(self::BLOCK_FORM)->getForm()->getId();
    }
}
