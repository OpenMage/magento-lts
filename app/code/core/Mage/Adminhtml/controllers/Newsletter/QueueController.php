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

use Mage_Newsletter_Model_Queue as Queue;

/**
 * Adminhtml newsletter queue controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Newsletter_QueueController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'newsletter/queue';

    /**
     * Queue list action
     */
    public function indexAction()
    {
        $this->_title($this->__('Newsletter'))->_title($this->__('Newsletter Queue'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->loadLayout();

        $this->_setActiveMenu('newsletter/queue');

        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/newsletter_queue', 'queue'),
        );

        $this->_addBreadcrumb(Mage::helper('newsletter')->__('Newsletter Queue'), Mage::helper('newsletter')->__('Newsletter Queue'));

        $this->renderLayout();
    }

    /**
     * Drop Newsletter queue template
     */
    public function dropAction()
    {
        $request = $this->getRequest();
        if ($request->getParam('text') && !$request->getPost('text')) {
            $this->getResponse()->setRedirect($this->getUrl('*/newsletter_queue'));
        }
        $this->loadLayout('newsletter_queue_preview');
        $this->renderLayout();
    }

    /**
     * Preview Newsletter queue template
     */
    public function previewAction()
    {
        $this->loadLayout();
        $data = $this->getRequest()->getParams();
        if (empty($data) || !isset($data['id'])) {
            $this->_forward('noRoute');
            return $this;
        }

        // set default value for selected store
        $data['preview_store_id'] = Mage::app()->getAnyStoreView()->getId();

        $this->getLayout()->getBlock('preview_form')->setFormData($data);
        $this->renderLayout();
    }

    /**
     * Queue list Ajax action
     */
    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/newsletter_queue_grid')->toHtml());
    }

    public function startAction()
    {
        $queue = Mage::getModel('newsletter/queue')
            ->load($this->getRequest()->getParam('id'));
        if ($queue->getId()) {
            if (!in_array($queue->getQueueStatus(), [Queue::STATUS_NEVER, Queue::STATUS_PAUSE])) {
                $this->_redirect('*/*');
                return;
            }

            $queue->setQueueStartAt(Mage::getSingleton('core/date')->gmtDate())
                ->setQueueStatus(Queue::STATUS_SENDING)
                ->save();
        }

        $this->_redirect('*/*');
    }

    public function pauseAction()
    {
        $queue = Mage::getSingleton('newsletter/queue')
            ->load($this->getRequest()->getParam('id'));

        if ($queue->getQueueStatus() != Queue::STATUS_SENDING) {
            $this->_redirect('*/*');
            return;
        }

        $queue->setQueueStatus(Queue::STATUS_PAUSE);
        $queue->save();

        $this->_redirect('*/*');
    }

    public function resumeAction()
    {
        $queue = Mage::getSingleton('newsletter/queue')
            ->load($this->getRequest()->getParam('id'));

        if ($queue->getQueueStatus() != Queue::STATUS_PAUSE) {
            $this->_redirect('*/*');
            return;
        }

        $queue->setQueueStatus(Queue::STATUS_SENDING);
        $queue->save();

        $this->_redirect('*/*');
    }

    public function cancelAction()
    {
        $queue = Mage::getSingleton('newsletter/queue')
            ->load($this->getRequest()->getParam('id'));

        if ($queue->getQueueStatus() != Queue::STATUS_SENDING) {
            $this->_redirect('*/*');
            return;
        }

        $queue->setQueueStatus(Queue::STATUS_CANCEL);
        $queue->save();

        $this->_redirect('*/*');
    }

    public function sendingAction()
    {
        // Todo: put it somewhere in config!
        $countOfQueue  = 3;
        $countOfSubscritions = 20;

        $collection = Mage::getResourceModel('newsletter/queue_collection')
            ->setPageSize($countOfQueue)
            ->setCurPage(1)
            ->addOnlyForSendingFilter()
            ->load();

        $collection->walk('sendPerSubscriber', [$countOfSubscritions]);
    }

    public function editAction()
    {
        $this->_title($this->__('Newsletter'))->_title($this->__('Newsletter Queue'));

        Mage::register('current_queue', Mage::getSingleton('newsletter/queue'));

        $id = $this->getRequest()->getParam('id');
        $templateId = $this->getRequest()->getParam('template_id');

        if ($id) {
            $queue = Mage::registry('current_queue')->load($id);
        } elseif ($templateId) {
            $template = Mage::getModel('newsletter/template')->load($templateId);
            $queue = Mage::registry('current_queue')->setTemplateId($template->getId());
        }

        $this->_title($this->__('Edit Queue'));

        $this->loadLayout();

        $this->_setActiveMenu('newsletter/queue');

        $this->_addBreadcrumb(
            Mage::helper('newsletter')->__('Newsletter Queue'),
            Mage::helper('newsletter')->__('Newsletter Queue'),
            $this->getUrl('*/newsletter_queue'),
        );
        $this->_addBreadcrumb(Mage::helper('newsletter')->__('Edit Queue'), Mage::helper('newsletter')->__('Edit Queue'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        try {
            /** @var Queue $queue */
            $queue = Mage::getModel('newsletter/queue');

            $templateId = $this->getRequest()->getParam('template_id');
            if ($templateId) {
                /** @var Mage_Newsletter_Model_Template $template */
                $template = Mage::getModel('newsletter/template')->load($templateId);

                if (!$template->getId() || $template->getIsSystem()) {
                    Mage::throwException($this->__('Wrong newsletter template.'));
                }

                $queue->setTemplateId($template->getId())
                    ->setQueueStatus(Queue::STATUS_NEVER);
            } else {
                $queue->load($this->getRequest()->getParam('id'));
            }

            if (!in_array($queue->getQueueStatus(), [Queue::STATUS_NEVER, Queue::STATUS_PAUSE])) {
                $this->_redirect('*/*');
                return;
            }

            if ($queue->getQueueStatus() == Queue::STATUS_NEVER) {
                $queue->setQueueStartAtByString($this->getRequest()->getParam('start_at'));
            }

            $queue->setStores($this->getRequest()->getParam('stores', []))
                ->setNewsletterSubject($this->getRequest()->getParam('subject'))
                ->setNewsletterSenderName($this->getRequest()->getParam('sender_name'))
                ->setNewsletterSenderEmail($this->getRequest()->getParam('sender_email'))
                ->setNewsletterText($this->getRequest()->getParam('text'))
                ->setNewsletterStyles($this->getRequest()->getParam('styles'));

            if ($queue->getQueueStatus() == Queue::STATUS_PAUSE
                && $this->getRequest()->getParam('_resume', false)
            ) {
                $queue->setQueueStatus(Queue::STATUS_SENDING);
            }

            $queue->save();
            $this->_redirect('*/*');
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $this->_redirect('*/*/edit', ['id' => $id]);
            } else {
                $this->_redirectReferer();
            }
        }
    }
}
