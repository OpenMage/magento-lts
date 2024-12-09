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
 * Adminhtml newsletter queue edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Newsletter_Queue_Edit extends Mage_Adminhtml_Block_Template
{
    public const BUTTON_PREVIEW           = 'preview_button';
    public const BUTTON_SAVE_AND_CONTINUE = 'save_and_resume';

    /**
     * Check for template Id in request
     */
    protected function _construct()
    {
        parent::_construct();
        $templateId = $this->getRequest()->getParam('template_id');
        if ($templateId) {
            $this->setTemplateId($templateId);
        }
    }

    /**
     * Retrieve current Newsletter Queue Object
     *
     * @return Mage_Newsletter_Model_Queue
     */
    public function getQueue()
    {
        return Mage::registry('current_queue');
    }

    /**
     * @inheritDoc
     */
    protected function _beforeToHtml()
    {
        $this->setTemplate('newsletter/queue/edit.phtml');

        $this->setChild(
            'form',
            $this->getLayout()->createBlock('adminhtml/newsletter_queue_edit_form', 'form')
        );

        return parent::_beforeToHtml();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSaveUrl()
    {
        if ($this->getTemplateId()) {
            $params = ['template_id' => $this->getTemplateId()];
        } else {
            $params = ['id' => $this->getRequest()->getParam('id')];
        }
        return $this->getUrl('*/*/save', $params);
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        // Load Wysiwyg on demand and Prepare layout
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            /** @var Mage_Page_Block_Html_Head $head */
            $head = $this->getLayout()->getBlock('head');
            if ($head) {
                $head->setCanLoadTinyMce(true);
            }
        }

        $this->addButtons();
        return parent::_prepareLayout();
    }

    /**
     * @codeCoverageIgnore
     */
    protected function addButtons(): void
    {
        $this->setChild(self::BUTTON_PREVIEW, $this->getButtonPreviewBlock());
        $this->setChild(self::BUTTON_SAVE, $this->getButtonSaveBlock());
        $this->setChild(self::BUTTON_SAVE_AND_CONTINUE, $this->getButtonSaveAndContinueBlock());
        $this->setChild(self::BUTTON_RESET, $this->getButtonResetBlock());
        $this->setChild(self::BUTTON_BACK, $this->getButtonBackBlock());
    }

    public function getButtonBackBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        $route = $this->getTemplateId() ? '*/newsletter_template/' : '*/*';
        return parent::getButtonBlockByType(self::BUTTON_BACK)
            ->setOnClick("window.location.href='" . $this->getUrl($route) . "'");
    }

    public function getButtonPreviewBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlock($name, $attributes)
            ->setLabel(Mage::helper('newsletter')->__('Preview Template'))
            ->setOnClick('queueControl.preview();')
            ->setClass(self::BUTTON__CLASS_TASK);
    }

    public function getButtonResetBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlockByType(self::BUTTON_RESET)
            ->setOnClick('window.location = window.location');
    }

    public function getButtonSaveBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlockByType(self::BUTTON_SAVE)
            ->setLabel(Mage::helper('newsletter')->__('Save Newsletter'))
            ->setOnClick('queueControl.save()');
    }

    public function getButtonSaveAndContinueBlock(): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBlockByType(self::BUTTON_SAVE_AND_CONTINUE)
            ->setLabel(Mage::helper('newsletter')->__('Save and Resume'))
            ->setOnClick('queueControl.resume()');
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
     * Retrieve Preview Button HTML
     *
     * @return string
     */
    public function getPreviewButtonHtml()
    {
        return $this->getChildHtml(self::BUTTON_PREVIEW);
    }

    /**
     * Retrieve Resume Button HTML
     *
     * @return string
     */
    public function getResumeButtonHtml()
    {
        return $this->getChildHtml(self::BUTTON_SAVE_AND_CONTINUE);
    }

    /**
     * Getter for availability preview mode
     *
     * @return bool
     */
    public function getIsPreview()
    {
        return !in_array($this->getQueue()->getQueueStatus(), [
            Mage_Newsletter_Model_Queue::STATUS_NEVER,
            Mage_Newsletter_Model_Queue::STATUS_PAUSE
        ]);
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

    /**
     * Getter for check is this newsletter the plain text.
     *
     * @return bool
     */
    public function getIsTextType()
    {
        return $this->getQueue()->isPlain();
    }

    /**
     * Getter for availability resume action
     *
     * @return bool
     */
    public function getCanResume()
    {
        return $this->getQueue()->getQueueStatus() == Mage_Newsletter_Model_Queue::STATUS_PAUSE;
    }

    /**
     * Getter for header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return ($this->getIsPreview() ? Mage::helper('newsletter')->__('View Newsletter') : Mage::helper('newsletter')->__('Edit Newsletter'));
    }
}
