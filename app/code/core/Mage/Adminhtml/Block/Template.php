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
 * Adminhtml abstract block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Template extends Mage_Core_Block_Template
{
    public const BUTTON_ADD                 = 'add_button';
    public const BUTTON_BACK                = 'back_button';
    public const BUTTON_CANCEL              = 'cancel_button';
    public const BUTTON_CLOSE               = 'close_button';
    public const BUTTON_DELETE              = 'delete_button';
    public const BUTTON_DUPLICATE           = 'duplicate_button';
    public const BUTTON_RESET               = 'reset_button';
    public const BUTTON_REFRESH             = 'refresh_button';
    public const BUTTON_SAVE                = 'save_button';
    public const BUTTON_SAVE_AND_CONTINUE   = 'save_and_edit_button';
    public const BUTTON_SUBMIT              = 'submit_button';
    public const BUTTON_UPDATE              = 'update_button';
    public const BUTTON_UPLOAD              = 'upload_button';

    public const BUTTON__CLASS_ADD          = 'add';
    public const BUTTON__CLASS_BACK         = 'back';
    public const BUTTON__CLASS_CANCEL       = 'cancel';
    public const BUTTON__CLASS_DELETE       = 'delete';
    public const BUTTON__CLASS_RESET        = 'reset';
    public const BUTTON__CLASS_SAVE         = 'save';
    public const BUTTON__CLASS_TASK         = 'task';
    public const BUTTON__CLASS_UPDATE       = 'update-button';

    /**
     * @return string
     */
    protected function _getUrlModelClass()
    {
        return 'adminhtml/url';
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string
     */
    public function getFormKey()
    {
        return Mage::getSingleton('core/session')->getFormKey();
    }

    /**
     * @param string $moduleName Full module name
     * @return bool
     * @deprecated
     * @see Mage_Core_Block_Template::isModuleOutputEnabled()
     */
    public function isOutputEnabled($moduleName = null)
    {
        return $this->isModuleOutputEnabled($moduleName);
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        Mage::dispatchEvent('adminhtml_block_html_before', ['block' => $this]);
        return parent::_toHtml();
    }

    /**
     * Deleting script tags from string
     *
     * @param string $html
     * @return string
     */
    public function maliciousCodeFilter($html)
    {
        return Mage::getSingleton('core/input_filter_maliciousCode')->filter($html);
    }

    private function createWidgetButtonBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        /** @var Mage_Adminhtml_Block_Widget_Button $block */
        $block = $this->getLayout()->createBlock('adminhtml/widget_button', $name, $attributes);
        return $block;
    }

    public function getButtonBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetButtonBlock($name, $attributes);
    }

    private function createWidgetAddButtonBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetButtonBlock($name, $attributes)
            ->setLabel(Mage::helper('adminhtml')->__('Add'))
            ->setClass(self::BUTTON__CLASS_ADD);
    }

    public function getButtonAddBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetAddButtonBlock($name, $attributes);
    }

    private function createWidgetBackButtonBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetButtonBlock($name, $attributes)
            ->setLabel(Mage::helper('adminhtml')->__('Back'))
            ->setOnClick("window.location.href='" . $this->getUrl('*/*') . "'")
            ->setClass(self::BUTTON__CLASS_BACK);
    }

    public function getButtonBackBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetBackButtonBlock($name, $attributes);
    }

    private function createWidgetCancelButtonBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetButtonBlock($name, $attributes)
            ->setLabel(Mage::helper('adminhtml')->__('Cancel'))
            ->setOnClick('window.close()')
            ->setClass(self::BUTTON__CLASS_CANCEL);
    }

    public function getButtonCancelBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetCancelButtonBlock($name, $attributes);
    }

    public function getButtonBackPopupBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetCancelButtonBlock($name, $attributes);
    }

    private function createWidgetCloseButtonBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetButtonBlock($name, $attributes)
            ->setLabel(Mage::helper('adminhtml')->__('Close Window'))
            ->setOnClick('window.close()');
    }

    public function getButtonCloseBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetCloseButtonBlock($name, $attributes);
    }

    private function createWidgetDeleteButtonBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetButtonBlock($name, $attributes)
            ->setLabel(Mage::helper('adminhtml')->__('Delete'))
            #->setOnClick(Mage::helper('core/js')->getConfirmSetLocationJs($this->getDeleteUrl()))
            ->setClass(self::BUTTON__CLASS_DELETE);
    }

    public function getButtonDeleteBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetDeleteButtonBlock($name, $attributes);
    }

    private function createWidgetDuplicateleteButtonBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetAddButtonBlock($name, $attributes)
            ->setLabel(Mage::helper('adminhtml')->__('Duplicate'))
            ->setOnClickSetLocationJsFullUrl($this->getDuplicateUrl());
    }

    public function getButtonDuplicateBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetDuplicateleteButtonBlock($name, $attributes);
    }

    private function createWidgetRefreshButtonBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetButtonBlock($name, $attributes)
            ->setLabel(Mage::helper('adminhtml')->__('Refresh'))
            ->setClass(self::BUTTON__CLASS_TASK);
    }

    public function getButtonRefreshBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetRefreshButtonBlock($name, $attributes);
    }

    private function createWidgetResetButtonBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetButtonBlock($name, $attributes)
            ->setLabel(Mage::helper('adminhtml')->__('Reset'))
            ->setOnClickSetLocationJsUrl('*/*/*', ['_current' => true])
            ->setClass(self::BUTTON__CLASS_RESET);
    }

    public function getButtonResetBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetResetButtonBlock($name, $attributes);
    }

    private function createWidgetSaveButtonBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetButtonBlock($name, $attributes)
            ->setLabel(Mage::helper('adminhtml')->__('Save'))
            ->setClass(self::BUTTON__CLASS_SAVE);
    }

    public function getButtonSaveBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetSaveButtonBlock($name, $attributes);
    }

    private function createWidgetSaveAndContinueButtonBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetButtonBlock($name, $attributes)
            ->setLabel(Mage::helper('adminhtml')->__('Save and Continue Edit'))
            ->setClass(self::BUTTON__CLASS_SAVE);
    }

    public function getButtonSaveAndContinueBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetSaveAndContinueButtonBlock($name, $attributes);
    }

    private function createWidgetUpdateButtonBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetButtonBlock($name, $attributes)
            ->setClass(self::BUTTON__CLASS_UPDATE);
    }

    public function getButtonUpdateBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetUpdateButtonBlock($name, $attributes);
    }

    private function createWidgetUploadButtonBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetButtonBlock($name, $attributes)
            ->setLabel(Mage::helper('adminhtml')->__('Upload Files'));
    }

    public function getButtonUploadBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return $this->createWidgetUploadButtonBlock($name, $attributes);
    }

    /**
     * Retrieve Add Button HTML
     *
     *  @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml(static::BUTTON_ADD);
    }

    /**
     * Retrieve Back Button HTML
     *
     *  @return string
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml(static::BUTTON_BACK);
    }

    /**
     * Retrieve Cancel Button HTML
     *
     *  @return string
     */
    public function getCancelButtonHtml()
    {
        return $this->getChildHtml(static::BUTTON_RESET);
    }

    /**
     * Retrieve Close Button HTML
     *
     *  @return string
     */
    public function getCloseButtonHtml()
    {
        return $this->getChildHtml(static::BUTTON_CLOSE);
    }

    /**
     * Retrieve Delete Button HTML
     *
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml(static::BUTTON_DELETE);
    }

    /**
     * Retrieve Delete Button HTML
     *
     * @return string
     */
    public function getDuplicateButtonHtml()
    {
        return $this->getChildHtml(static::BUTTON_DUPLICATE);
    }

    /**
     * Retrieve Reset Button HTML
     *
     * @return string
     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml(static::BUTTON_RESET);
    }

    /**
     * Retrieve Save Button HTML
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml(static::BUTTON_SAVE);
    }

    public function getSaveAndEditButtonHtml(): string
    {
        return $this->getChildHtml(static::BUTTON_SAVE_AND_CONTINUE);
    }

    /**
     * Retrieve Update Button HTML
     *
     * @return string
     */
    public function getUpdateButtonHtml()
    {
        return $this->getChildHtml(static::BUTTON_UPDATE);
    }

    /**
     * Retrieve Upload Button HTML
     *
     * @return string
     */
    public function getUploadButtonHtml()
    {
        return $this->getChildHtml(static::BUTTON_UPLOAD);
    }
}
