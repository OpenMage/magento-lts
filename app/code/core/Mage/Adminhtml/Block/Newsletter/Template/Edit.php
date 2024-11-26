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
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled() && ($block = $this->getLayout()->getBlock('head'))) {
            $block->setCanLoadTinyMce(true);
        }

        $this->setChild(
            'back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('newsletter')->__('Back'),
                    'onclick'   => "window.location.href = '" . $this->getUrl('*/*') . "'",
                    'class'     => 'back'
                ])
        );

        $this->setChild(
            'reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('newsletter')->__('Reset'),
                    'onclick'   => 'window.location.href = window.location.href'
                ])
        );

        $this->setChild(
            'to_plain_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('newsletter')->__('Convert to Plain Text'),
                    'onclick'   => 'templateControl.stripTags();',
                    'id'        => 'convert_button',
                    'class'     => 'task'
                ])
        );

        $this->setChild(
            'to_html_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('newsletter')->__('Return HTML Version'),
                    'onclick'   => 'templateControl.unStripTags();',
                    'id'        => 'convert_button_back',
                    'style'     => 'display:none',
                    'class'     => 'task'
                ])
        );

        $this->setChild(
            'save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('newsletter')->__('Save Template'),
                    'onclick'   => 'templateControl.save();',
                    'class'     => 'save'
                ])
        );

        $this->setChild(
            'save_as_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('newsletter')->__('Save As'),
                    'onclick'   => 'templateControl.saveAs();',
                    'class'     => 'save'
                ])
        );

        $this->setChild(
            'preview_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('newsletter')->__('Preview Template'),
                    'onclick'   => 'templateControl.preview();',
                    'class'     => 'task'
                ])
        );

        $this->setChild(
            'delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('newsletter')->__('Delete Template'),
                    'onclick'   => 'templateControl.deleteTemplate();',
                    'class'     => 'delete'
                ])
        );

        return parent::_prepareLayout();
    }

    /**
     * Retrieve Back Button HTML
     *
     * @return string
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    /**
     * Retrieve Reset Button HTML
     *
     * @return string
     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    /**
     * Retrieve Convert To Plain Button HTML
     *
     * @return string
     */
    public function getToPlainButtonHtml()
    {
        return $this->getChildHtml('to_plain_button');
    }

    /**
     * Retrieve Convert to HTML Button HTML
     *
     * @return string
     */
    public function getToHtmlButtonHtml()
    {
        return $this->getChildHtml('to_html_button');
    }

    /**
     * Retrieve Save Button HTML
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * Retrieve Preview Button HTML
     *
     * @return string
     */
    public function getPreviewButtonHtml()
    {
        return $this->getChildHtml('preview_button');
    }

    /**
     * Retrieve Delete Button HTML
     *
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * Retrieve Save as Button HTML
     *
     * @return string
     */
    public function getSaveAsButtonHtml()
    {
        return $this->getChildHtml('save_as_button');
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
