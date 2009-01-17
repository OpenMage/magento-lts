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
 * @category   Mage
 * @package    Mage_Newsletter
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Newsletter subscribe controller
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Newsletter_SubscriberController extends Mage_Core_Controller_Front_Action
{
    /**
 	 * New subscription action
 	 */
    public function newAction()
    {
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
        	$session   = Mage::getSingleton('core/session');
        	$email     = (string) $this->getRequest()->getPost('email');
        	try {
                $status = Mage::getModel('newsletter/subscriber')->subscribe($email);
                if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                    $session->addSuccess($this->__('Confirmation request has been sent'));
                }
                else {
                    $session->addSuccess($this->__('Thank you for your subscription'));
                }
        	}
        	catch (Mage_Core_Exception $e) {
        	    $session->addException($e, $this->__('There was a problem with the subscription: %s', $e->getMessage()));
        	}
        	catch (Exception $e) {
        	    $session->addException($e, $this->__('There was a problem with the subscription'));
        	}
        }
        $this->_redirectReferer();
    }

    /**
     * Subscription confirm action
     */
    public function confirmAction()
    {
    	$id    = (int) $this->getRequest()->getParam('id');
    	$code  = (string) $this->getRequest()->getParam('code');

    	if ($id && $code) {
        	$subscriber = Mage::getModel('newsletter/subscriber')->load($id);
            $session = Mage::getSingleton('core/session');

        	if($subscriber->getId() && $subscriber->getCode()) {
                if($subscriber->confirm($code)) {
                    $session->addSuccess($this->__('Your subscription was successfully confirmed'));
                } else {
                    $session->addError($this->__('Invalid subscription confirmation code'));
                }
        	} else {
                $session->addError($this->__('Invalid subscription ID'));
        	}
    	}

        $this->_redirectUrl(Mage::getBaseUrl());
    }

    /**
     * Unsubscribe newsletter
     */
    public function unsubscribeAction()
    {
    	$id    = (int) $this->getRequest()->getParam('id');
    	$code  = (string) $this->getRequest()->getParam('code');

    	if ($id && $code) {
        	$session = Mage::getSingleton('core/session');
        	try {
            	$result  = Mage::getModel('newsletter/subscriber')->load($id)
                    ->setCheckCode($code)
                    ->unsubscribe();
                $session->addSuccess($this->__('You have been successfully unsubscribed.'));
        	}
        	catch (Mage_Core_Exception $e) {
                $session->addException($e, $e->getMessage());
        	}
            catch (Exception $e) {
                $session->addException($e, $this->__('There was a problem with the un-subscription.'));
            }
    	}
        $this->_redirectReferer();
    }
}