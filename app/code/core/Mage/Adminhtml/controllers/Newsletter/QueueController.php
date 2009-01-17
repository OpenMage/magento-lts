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
 * Adminhtml newsletter queue controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Newsletter_QueueController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Queue list action
     */
    public function indexAction()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->loadLayout();

        $this->_setActiveMenu('newsletter/queue');

        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/newsletter_queue', 'queue')
        );

        $this->_addBreadcrumb(Mage::helper('newsletter')->__('Newsletter Queue'), Mage::helper('newsletter')->__('Newsletter Queue'));

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
    		if (!in_array($queue->getQueueStatus(),
	    		 		 array(Mage_Newsletter_Model_Queue::STATUS_NEVER,
	    		 		 	   Mage_Newsletter_Model_Queue::STATUS_PAUSE))) {
	   			$this->_redirect('*/*');
	    		return;
	    	}

    		$queue->setQueueStartAt(Mage::getSingleton('core/date')->gmtDate())
    			->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_SENDING)
    			->save();
    	}

    	$this->_redirect('*/*');
    }

    public function pauseAction()
    {
    	$queue = Mage::getSingleton('newsletter/queue')
    		->load($this->getRequest()->getParam('id'));

    	if (!in_array($queue->getQueueStatus(),
    		 		 array(Mage_Newsletter_Model_Queue::STATUS_SENDING))) {
   			$this->_redirect('*/*');
    		return;
    	}

    	$queue->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_PAUSE);
    	$queue->save();

    	$this->_redirect('*/*');
    }

    public function resumeAction()
    {
    	$queue = Mage::getSingleton('newsletter/queue')
    		->load($this->getRequest()->getParam('id'));

    	if (!in_array($queue->getQueueStatus(),
    		 		 array(Mage_Newsletter_Model_Queue::STATUS_PAUSE))) {
   			$this->_redirect('*/*');
    		return;
    	}

    	$queue->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_SENDING);
    	$queue->save();

    	$this->_redirect('*/*');
    }

    public function cancelAction()
    {
    	$queue = Mage::getSingleton('newsletter/queue')
    		->load($this->getRequest()->getParam('id'));

    	if (!in_array($queue->getQueueStatus(),
    		 		 array(Mage_Newsletter_Model_Queue::STATUS_SENDING))) {
   			$this->_redirect('*/*');
    		return;
    	}

    	$queue->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_CANCEL);
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

        $collection->walk('sendPerSubscriber', array($countOfSubscritions));
    }


    public function editAction()
    {
    	$queue = Mage::getSingleton('newsletter/queue')
    		->load($this->getRequest()->getParam('id'));


    	$this->loadLayout();

    	$this->_setActiveMenu('newsletter/queue');

        $this->_addBreadcrumb(Mage::helper('newsletter')->__('Newsletter Queue'), Mage::helper('newsletter')->__('Newsletter Queue'), $this->getUrl('*/newsletter_queue'));
        $this->_addBreadcrumb(Mage::helper('newsletter')->__('Edit Queue'), Mage::helper('newsletter')->__('Edit Queue'));

        $this->_addContent(
        	$this->getLayout()->createBlock('adminhtml/newsletter_queue_edit', 'queue.edit')
        );

    	$this->renderLayout();
    }

    public function saveAction()
    {
    	$queue = Mage::getSingleton('newsletter/queue')
    		->load($this->getRequest()->getParam('id'));

    	if (!in_array($queue->getQueueStatus(),
    		 		 array(Mage_Newsletter_Model_Queue::STATUS_NEVER,
    		 		 	   Mage_Newsletter_Model_Queue::STATUS_PAUSE))) {
   			$this->_redirect('*/*');
    		return;
    	}

    	$format = Mage::app()->getLocale()->getDateTimeFormat(
            Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM
        );

    	if ($queue->getQueueStatus()==Mage_Newsletter_Model_Queue::STATUS_NEVER) {
    	    if ($this->getRequest()->getParam('start_at')) {
    	        $date = Mage::app()->getLocale()->date($this->getRequest()->getParam('start_at'), $format);
    	        $time = $date->getTimestamp();
	    		$queue->setQueueStartAt(
	    			Mage::getModel('core/date')->gmtDate(null, $time)
	    		);
	    	} else {
	    		$queue->setQueueStartAt(null);
	    	}
    	}

    	$queue->setStores($this->getRequest()->getParam('stores', array()));

    	$queue->addTemplateData($queue);
    	$queue->getTemplate()
    		->setTemplateSubject($this->getRequest()->getParam('subject'))
    		->setTemplateSenderName($this->getRequest()->getParam('sender_name'))
    		->setTemplateSenderEmail($this->getRequest()->getParam('sender_email'))
    		->setTemplateTextPreprocessed($this->getRequest()->getParam('text'));

    	if ($queue->getQueueStatus() == Mage_Newsletter_Model_Queue::STATUS_PAUSE
    		&& $this->getRequest()->getParam('_resume', false)) {
    		$queue->setQueueStatus(Mage_Newsletter_Model_Queue::STATUS_SENDING);
    	}

    	$queue->setSaveTemplateFlag(true);

    	try {
    		$queue->save();
    	}
    	catch (Exception $e) {
    		echo $e->getMessage();
            exit;
    	}
    	$this->_redirect('*/*');
    }

    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('newsletter/queue');
    }
}
