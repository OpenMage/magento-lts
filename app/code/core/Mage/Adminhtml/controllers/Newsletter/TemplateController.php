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
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Newsletter admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Newsletter_TemplateController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
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

    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/newsletter_template_grid')->toHtml());
    }



    public function newAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('newsletter/template');
        $this->_addBreadcrumb(Mage::helper('newsletter')->__('Newsletter Templates'), Mage::helper('newsletter')->__('Newsletter Templates'), $this->getUrl('*/*'));

        if ($this->getRequest()->getParam('id')) {
            $this->_addBreadcrumb(Mage::helper('newsletter')->__('Edit Template'), Mage::helper('newsletter')->__('Edit Newsletter Template'));
        } else {
            $this->_addBreadcrumb(Mage::helper('newsletter')->__('New Template'), Mage::helper('newsletter')->__('Create Newsletter Template'));
        }

        $this->_addContent($this->getLayout()->createBlock('adminhtml/newsletter_template_edit', 'template_edit')
                                                            ->setEditMode((bool)$this->getRequest()->getParam('id')));
        $this->renderLayout();
    }


    public function editAction()
    {
        $this->_forward('new');
    }

    public function saveAction()
    {
        $request = $this->getRequest();
        $template = Mage::getModel('newsletter/template');
        if ($id = (int)$request->getParam('id')) {
            $template->load($id);
        }

        try {
            $template->setTemplateSubject($request->getParam('subject'))
                ->setTemplateCode($request->getParam('code'))
                ->setTemplateSenderEmail($request->getParam('sender_email'))
                ->setTemplateSenderName($request->getParam('sender_name'))
                ->setTemplateText($request->getParam('text'))
                ->setModifiedAt(Mage::getSingleton('core/date')->gmtDate());

            if (!$template->getId()) {
                $type = constant(Mage::getConfig()->getModelClassName('newsletter/template') . "::TYPE_HTML");
                $template->setTemplateType($type);
            }

            if($this->getRequest()->getParam('_change_type_flag')) {
                $type = constant(Mage::getConfig()->getModelClassName('newsletter/template') . "::TYPE_TEXT");
                $template->setTemplateType($type);
            }

            if($this->getRequest()->getParam('_save_as_flag')) {
                $template->setId(null);
            }

            $template->save();
            $this->_redirect('*/*');
        }
        catch (Exception $e) {
        	Mage::getSingleton('adminhtml/session')->setData('newsletter_template_form_data', $this->getRequest()->getParams());
        	Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        	$this->_forward('new');
        }

    }

    public function deleteAction()
    {

        $template = Mage::getModel('newsletter/template');
        $id = (int)$this->getRequest()->getParam('id');
        $template->load($id);
        if($template->getId()) {
            try {
                $template->delete();
            }
            catch (Exception $e) {
                // Nothing
            }
        }
        $this->_redirect('*/*');
    }

    public function previewAction()
    {
        $this->loadLayout('preview');
        $this->renderLayout();
    }

    public function toqueueAction()
    {
    	$template = Mage::getModel('newsletter/template')
        	->load($this->getRequest()->getParam('id'));

        if(!$template->getIsSystem()) {
            $template->preprocess();

			$queue = Mage::getModel('newsletter/queue')
				->setTemplateId($this->getRequest()->getParam('id'))
		    	->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_NEVER);

		    $queue->save();
		    $template->save();

		    $this->_redirect('*/newsletter_queue/edit', array('id'=>$queue->getId()));
        }
    }

    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('newsletter/template');
    }
}