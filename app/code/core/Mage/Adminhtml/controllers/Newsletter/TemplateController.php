<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Manage Newsletter Template Controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Newsletter_TemplateController extends Mage_Adminhtml_Controller_Action
{
    /**
     * View Templates list
     *
     */
    public function indexAction ()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->loadLayout();
        $this->_setActiveMenu('newsletter/template');
        $this->_addBreadcrumb(Mage::helper('newsletter')->__('Newsletter Templates'), Mage::helper('newsletter')->__('Newsletter Templates'));
        $this->_addContent($this->getLayout()->createBlock('adminhtml/newsletter_template', 'template'));
        $this->renderLayout();
    }

    /**
     * JSON Grid Action
     *
     */
    public function gridAction ()
    {
        $this->loadLayout();
        $grid = $this->getLayout()->createBlock('adminhtml/newsletter_template_grid')
            ->toHtml();
        $this->getResponse()->setBody($grid);
    }

    /**
     * Create new Nesletter Template
     *
     */
    public function newAction ()
    {
        $this->_forward('edit');
    }

    /**
     * Edit Newsletter Template
     *
     */
    public function editAction ()
    {
        $model = Mage::getModel('newsletter/template');
        if ($id = $this->getRequest()->getParam('id')) {
            $model->load($id);
        }

        Mage::register('_current_template', $model);

        $this->loadLayout();
        $this->_setActiveMenu('newsletter/template');

        if ($model->getId()) {
            $breadcrumbTitle = Mage::helper('newsletter')->__('Edit Template');
            $breadcrumbLabel = $breadcrumbTitle;
        }
        else {
            $breadcrumbTitle = Mage::helper('newsletter')->__('New Template');
            $breadcrumbLabel = Mage::helper('newsletter')->__('Create Newsletter Template');
        }

        $this->_addBreadcrumb($breadcrumbLabel, $breadcrumbTitle);

        // restore data
        if ($values = $this->_getSession()->getData('newsletter_template_form_data', true)) {
            $model->addData($values);
        }

        $content = $this->getLayout()
            ->createBlock('adminhtml/newsletter_template_edit', 'template_edit')
            ->setEditMode($model->getId() > 0);
        $this->_addContent($content);
        $this->renderLayout();
    }

    /**
     * Save Nesletter Template
     *
     */
    public function saveAction ()
    {
        $request = $this->getRequest();
        $template = Mage::getModel('newsletter/template');

        if ($id = (int)$request->getParam('id')) {
            $template->load($id);
        }

        try {
            $template->addData($request->getParams())
                ->setTemplateSubject($request->getParam('subject'))
                ->setTemplateCode($request->getParam('code'))
                ->setTemplateSenderEmail($request->getParam('sender_email'))
                ->setTemplateSenderName($request->getParam('sender_name'))
                ->setTemplateText($request->getParam('text'))
                ->setModifiedAt(Mage::getSingleton('core/date')->gmtDate());

            if (!$template->getId()) {
                $template->setTemplateType(Mage_Newsletter_Model_Template::TYPE_HTML);
            }
            if ($this->getRequest()->getParam('_change_type_flag')) {
                $template->setTemplateType(Mage_Newsletter_Model_Template::TYPE_TEXT);
            }
            if ($this->getRequest()->getParam('_save_as_flag')) {
                $template->setId(null);
            }
            $template->save();
            $this->_redirect('*/*');
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError(nl2br($e->getMessage()));
            $this->_getSession()->setData('newsletter_template_form_data',
                $this->getRequest()->getParams());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('Error while saving this template. Please try again later.'));
            $this->_getSession()->setData('newsletter_template_form_data', $this->getRequest()->getParams());
        }
        $this->_forward('new');
    }

    /**
     * Delete newsletter Template
     *
     */
    public function deleteAction ()
    {
        $template = Mage::getModel('newsletter/template')
            ->load($this->getRequest()->getParam('id'));
        if ($template->getId()) {
            try {
                $template->delete();
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('Error while deleting this template. Please try again later.'));
            }
        }
        $this->_redirect('*/*');
    }

    /**
     * Preview Newsletter template
     *
     */
    public function previewAction ()
    {
        $this->loadLayout('preview');
        $this->renderLayout();
    }

    /**
     * Queue Newsletter
     *
     */
    public function toqueueAction ()
    {
        $template = Mage::getModel('newsletter/template')
            ->load($this->getRequest()->getParam('id'));
        if (!$template->getIsSystem()) {
            $template->preprocess();
            $queue = Mage::getModel('newsletter/queue')
                ->setTemplateId($template->getId())
                ->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_NEVER)
                ->save();
            $template->save();
            $this->_redirect('*/newsletter_queue/edit', array('id' => $queue->getId()));
        }
        else {
            $this->_redirect('*/*');
        }
    }

    /**
     * Check is allowed access
     *
     * @return bool
     */
    protected function _isAllowed ()
    {
        return Mage::getSingleton('admin/session')
            ->isAllowed('newsletter/template');
    }
}
