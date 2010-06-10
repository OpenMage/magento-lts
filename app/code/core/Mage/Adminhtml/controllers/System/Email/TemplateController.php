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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * System Template admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_System_Email_TemplateController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('System'))->_title($this->__('Transactional Emails'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->loadLayout();
        $this->_setActiveMenu('system/email_template');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Transactional Emails'), Mage::helper('adminhtml')->__('Transactional Emails'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/system_email_template', 'template'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/system_email_template_grid')->toHtml());
    }


    /**
     * New transactional email action
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit transactioanl email action
     *
     */
    public function editAction()
    {
        $this->loadLayout();
        $template = $this->_initTemplate('id');
        $this->_setActiveMenu('system/email_template');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Transactional Emails'), Mage::helper('adminhtml')->__('Transactional Emails'), $this->getUrl('*/*'));

        if ($this->getRequest()->getParam('id')) {
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit Template'), Mage::helper('adminhtml')->__('Edit System Template'));
        } else {
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('New Template'), Mage::helper('adminhtml')->__('New System Template'));
        }

        $this->_title($template->getId() ? $template->getTemplateCode() : $this->__('New Template'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/system_email_template_edit', 'template_edit')
                                                            ->setEditMode((bool)$this->getRequest()->getParam('id')));
        $this->renderLayout();
    }

    public function saveAction()
    {
        $request = $this->getRequest();
        $id = $this->getRequest()->getParam('id');

        $template = $this->_initTemplate('id');
        if (!$template->getId() && $id) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('This Email template no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $template->setTemplateSubject($request->getParam('template_subject'))
                ->setTemplateCode($request->getParam('template_code'))
/*
                ->setTemplateSenderEmail($request->getParam('sender_email'))
                ->setTemplateSenderName($request->getParam('sender_name'))
*/
                ->setTemplateText($request->getParam('template_text'))
                ->setTemplateStyles($request->getParam('template_styles'))
                ->setModifiedAt(Mage::getSingleton('core/date')->gmtDate())
                ->setOrigTemplateCode($request->getParam('orig_template_code'))
                ->setOrigTemplateVariables($request->getParam('orig_template_variables'));

            if (!$template->getId()) {
                //$type = constant(Mage::getConfig()->getModelClassName('core/email_template') . "::TYPE_HTML");
                $template->setTemplateType(Mage_Core_Model_Email_Template::TYPE_HTML);
            }

            if($request->getParam('_change_type_flag')) {
                //$type = constant(Mage::getConfig()->getModelClassName('core/email_template') . "::TYPE_TEXT");
                $template->setTemplateType(Mage_Core_Model_Email_Template::TYPE_TEXT);
                $template->setTemplateStyles('');
            }

            $template->save();
            Mage::getSingleton('adminhtml/session')->setFormData(false);
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The email template has been saved.'));
            $this->_redirect('*/*');
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->setData('email_template_form_data', $this->getRequest()->getParams());
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_forward('new');
        }

    }

    public function deleteAction() {

        $template = $this->_initTemplate('id');
        if($template->getId()) {
            try {
                $template->delete();
                 // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The email template has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('adminhtml')->__('An error occurred while deleting email template data. Please review log and try again.'));
                Mage::logException($e);
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Unable to find a Email Template to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    public function previewAction()
    {
        $this->loadLayout('systemPreview');
        $this->renderLayout();
    }

    /**
     * Set template data to retrieve it in template info form
     *
     */
    public function defaultTemplateAction()
    {
        $template = $this->_initTemplate('id');
        $templateCode = $this->getRequest()->getParam('code');

        $template->loadDefault($templateCode, $this->getRequest()->getParam('locale'));
        $template->setData('orig_template_code', $templateCode);
        $template->setData('template_variables', Zend_Json::encode($template->getVariablesOptionArray(true)));

        $templateBlock = $this->getLayout()->createBlock('adminhtml/system_email_template_edit');
        $template->setData('orig_template_used_default_for', $templateBlock->getUsedDefaultForPaths(false));

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($template->getData()));
    }

    /**
     * Load email template from request
     *
     * @param string $idFieldName
     * @return Mage_Adminhtml_Model_Email_Template $model
     */
    protected function _initTemplate($idFieldName = 'template_id')
    {
        $this->_title($this->__('System'))->_title($this->__('Transactional Emails'));

        $id = (int)$this->getRequest()->getParam($idFieldName);
        $model = Mage::getModel('adminhtml/email_template');
        if ($id) {
            $model->load($id);
        }
        if (!Mage::registry('email_template')) {
            Mage::register('email_template', $model);
        }
        if (!Mage::registry('current_email_template')) {
            Mage::register('current_email_template', $model);
        }
        return $model;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/email_template');
    }
}
