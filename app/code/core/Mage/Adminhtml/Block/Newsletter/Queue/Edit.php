<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml newsletter queue edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Newsletter_Queue_Edit extends Mage_Adminhtml_Block_Template
{
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
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }

        $this->setChild(
            'preview_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('newsletter')->__('Preview Template'),
                    'onclick'   => 'queueControl.preview();',
                    'class'     => 'task'
                ])
        );

        $this->setChild(
            'save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('newsletter')->__('Save Newsletter'),
                    'onclick'   => 'queueControl.save()',
                    'class'     => 'save'
                ])
        );

        $this->setChild(
            'save_and_resume',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('newsletter')->__('Save and Resume'),
                    'onclick'   => 'queueControl.resume()',
                    'class'     => 'save'
                ])
        );

        $this->setChild(
            'reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('newsletter')->__('Reset'),
                    'onclick'   => 'window.location = window.location'
                ])
        );

        $this->setChild(
            'back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    [
                        'label'   => Mage::helper('newsletter')->__('Back'),
                        'onclick' => "window.location.href = '" . $this->getUrl((
                            $this->getTemplateId() ? '*/newsletter_template/' : '*/*'
                        )) . "'",
                        'class'   => 'back'
                    ]
                )
        );

        return parent::_prepareLayout();
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
        return $this->getChildHtml('preview_button');
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
     * Retrieve Reset Button HTML
     *
     * @return string
     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
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
     * Retrieve Resume Button HTML
     *
     * @return string
     */
    public function getResumeButtonHtml()
    {
        return $this->getChildHtml('save_and_resume');
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
