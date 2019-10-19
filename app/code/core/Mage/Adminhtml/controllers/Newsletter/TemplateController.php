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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
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
     * Check is allowed access
     *
     * @return bool
     */
    protected function _isAllowed ()
    {
        return Mage::getSingleton('admin/session')
            ->isAllowed('newsletter/template');
    }

    /**
     * Set title of page
     *
     * @return Mage_Adminhtml_Newsletter_TemplateController
     */
    protected function _setTitle()
    {
        return $this->_title($this->__('Newsletter'))->_title($this->__('Newsletter Templates'));
    }

    /**
     * View Templates list
     *
     */
    public function indexAction ()
    {
        $this->_setTitle();

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
     * Create new Newsletter Template
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
        $this->_setTitle();

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

        $this->_title($model->getId() ? $model->getTemplateCode() : $this->__('New Template'));

        $this->_addBreadcrumb($breadcrumbLabel, $breadcrumbTitle);

        // restore data
        if ($values = $this->_getSession()->getData('newsletter_template_form_data', true)) {
            $model->addData($values);
        }

        if ($editBlock = $this->getLayout()->getBlock('template_edit')) {
            $editBlock->setEditMode($model->getId() > 0);
        }

        $this->renderLayout();
    }

    /**
     * Drop Newsletter Template
     *
     */
    public function dropAction ()
    {
        $request = $this->getRequest();
        if ($request->getParam('text') && !$request->getPost('text')) {
             $this->getResponse()->setRedirect($this->getUrl('*/newsletter_template'));
        }
        $this->loadLayout('newsletter_template_preview');
        $this->renderLayout();
    }

    /**
     * Save Newsletter Template
     *
     */
    public function saveAction ()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/newsletter_template'));
        }
        $template = Mage::getModel('newsletter/template');

        if ($id = (int)$request->getParam('id')) {
            $template->load($id);
        }

        try {
            $allowedHtmlTags = ['text', 'styles'];
            if (Mage::helper('adminhtml')->hasTags($request->getParams(), $allowedHtmlTags)) {
                Mage::throwException(Mage::helper('adminhtml')->__('Invalid template data.'));
            }

            $template->addData($request->getParams())
                ->setTemplateSubject($request->getParam('subject'))
                ->setTemplateCode($request->getParam('code'))
                ->setTemplateSenderEmail($request->getParam('sender_email'))
                ->setTemplateSenderName($request->getParam('sender_name'))
                ->setTemplateText($request->getParam('text'))
                ->setTemplateStyles($request->getParam('styles'))
                ->setModifiedAt(Mage::getSingleton('core/date')->gmtDate());

            if (!$template->getId()) {
                $template->setTemplateType(Mage_Newsletter_Model_Template::TYPE_HTML);
            }
            if ($this->getRequest()->getParam('_change_type_flag')) {
                $template->setTemplateType(Mage_Newsletter_Model_Template::TYPE_TEXT);
                $template->setTemplateStyles('');
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
            $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('An error occurred while saving this template.'));
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
                $this->_getSession()->addException($e, Mage::helper('adminhtml')->__('An error occurred while deleting this template.'));
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
        $this->_setTitle();
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
     * Controller predispatch method
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    public function preDispatch()
    {
        $this->_setForcedFormKeyActions('delete');
        return parent::preDispatch();
    }
}
