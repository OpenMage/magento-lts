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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Newsletter Template Edit Block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Newsletter_Template_Edit extends Mage_Adminhtml_Block_Widget
{
    public const BUTTON_PREVIEW  = 'preview_button';
    public const BUTTON_SAVE_AS  = 'save_as_button';
    public const BUTTON_TO_HTML  = 'to_html_button';
    public const BUTTON_TO_PLAIN = 'to_plain_button';

    /**
     * Edit Block model
     *
     * @var bool
     */
    protected $_editMode = false;

    /**
     * Retrieve template object
     *
     * @return Mage_Newsletter_Model_Template
     */
    public function getModel()
    {
        return Mage::registry('_current_template');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        // Load Wysiwyg on demand and Prepare layout
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()
            && ($block = $this->getLayout()->getBlock('head'))
        ) {
            $block->setCanLoadTinyMce(true);
        }

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
        $this->setChild(self::BUTTON_TO_PLAIN, $this->getButtonToPlainBlock());
        $this->setChild(self::BUTTON_TO_HTML, $this->getButtonToHtmlBlock());
        $this->setChild(self::BUTTON_SAVE, $this->getButtonSaveBlock());
        $this->setChild(self::BUTTON_SAVE_AS, $this->getButtonSaveAsBlock());
        $this->setChild(self::BUTTON_PREVIEW, $this->getButtonPreviewBlock());
        $this->setChild(self::BUTTON_DELETE, $this->getButtonDeleteBlock());
    }

    public function getButtonBackBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBackBlock($name, $attributes)
            ->setOnClick("window.location.href = '" . $this->getUrl('*/*') . "'");
    }

    public function getButtonDeleteBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonDeleteBlock($name, $attributes)
            ->setLabel(Mage::helper('newsletter')->__('Delete Template'))
            ->setOnClick('templateControl.deleteTemplate();');
    }

    public function getButtonPreviewBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlock($name, $attributes)
            ->setLabel(Mage::helper('newsletter')->__('Preview Template'))
            ->setOnClick('templateControl.preview();')
            ->setClass(self::BUTTON__CLASS_TASK);
    }

    public function getButtonResetBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonResetBlock($name, $attributes)
            ->setOnClick('window.location.href = window.location.href')
            ->resetClass();
    }

    public function getButtonSaveBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonSaveBlock($name, $attributes)
            ->setLabel(Mage::helper('newsletter')->__('Save Template'))
            ->setOnClick('templateControl.save();');
    }

    public function getButtonSaveAsBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonSaveBlock($name, $attributes)
            ->setLabel(Mage::helper('newsletter')->__('Save As'))
            ->setOnClick('templateControl.saveAs();');
    }

    public function getButtonToHtmlBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlock($name, $attributes)
            ->setId('convert_button_back')
            ->setLabel(Mage::helper('newsletter')->__('Return HTML Version'))
            ->setOnClick('templateControl.unStripTags();')
            ->setClass(self::BUTTON__CLASS_TASK)
            ->setStyle('display:none');
    }

    public function getButtonToPlainBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlock($name, $attributes)
            ->setId('convert_button')
            ->setLabel(Mage::helper('newsletter')->__('Convert to Plain Text'))
            ->setOnClick('templateControl.stripTags();')
            ->setClass(self::BUTTON__CLASS_TASK);
    }

    /**
     * Retrieve Convert To Plain Button HTML
     *
     * @return string
     */
    public function getToPlainButtonHtml()
    {
        return $this->getChildHtml(self::BUTTON_TO_PLAIN);
    }

    /**
     * Retrieve Convert to HTML Button HTML
     *
     * @return string
     */
    public function getToHtmlButtonHtml()
    {
        return $this->getChildHtml(self::BUTTON_TO_HTML);
    }

    /**
     * Retrieve Preview Button HTML
     *
     * @return string
     */
    public function getPreviewButtonHtml()
    {
        return $this->getChildHtml(self::BUTTON_PREVIEW);
    }

    /**
     * Retrieve Save as Button HTML
     *
     * @return string
     */
    public function getSaveAsButtonHtml()
    {
        return $this->getChildHtml(self::BUTTON_SAVE_AS);
    }

    /**
     * Set edit flag for block
     *
     * @param bool $value
     * @return $this
     */
    public function setEditMode($value = true)
    {
        $this->_editMode = (bool)$value;
        return $this;
    }

    /**
     * Return edit flag for block
     *
     * @return bool
     */
    public function getEditMode()
    {
        return $this->_editMode;
    }

    /**
     * Return header text for form
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getEditMode()) {
            return Mage::helper('newsletter')->__('Edit Newsletter Template');
        }
        return  Mage::helper('newsletter')->__('New Newsletter Template');
    }

    /**
     * Return form block HTML
     *
     * @return string
     */
    public function getForm()
    {
        return $this->getLayout()
            ->createBlock('adminhtml/newsletter_template_edit_form')
            ->toHtml();
    }

    /**
     * @return string
     */
    public function getJsTemplateName()
    {
        $templateCode = $this->getModel()->getTemplateCode();
        if ($templateCode === null) {
            return '';
        }
        // phpcs:ignore Ecg.Security.ForbiddenFunction.Found
        return addcslashes($this->escapeHtml($templateCode), "\"\r\n\\");
    }

    /**
     * Return action url for form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save');
    }

    /**
     * Return preview action url for form
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->getUrl('*/*/preview');
    }

    /**
     * Check Template Type is Plain Text
     *
     * @return bool
     */
    public function isTextType()
    {
        return $this->getModel()->isPlain();
    }

    /**
     * Return delete url for customer group
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrlSecure('*/*/delete', ['id' => $this->getRequest()->getParam('id')]);
    }

    /**
     * Retrieve Save As Flag
     *
     * @return string
     */
    public function getSaveAsFlag()
    {
        return $this->getRequest()->getParam('_save_as_flag') ? '1' : '';
    }

    /**
     * Getter for single store mode check
     *
     * @return bool
     */
    protected function isSingleStoreMode()
    {
        return Mage::app()->isSingleStoreMode();
    }

    /**
     * Getter for id of current store (the only one in single-store mode and current in multi-stores mode)
     *
     * @return int
     */
    protected function getStoreId()
    {
        return Mage::app()->getStore(true)->getId();
    }
}
