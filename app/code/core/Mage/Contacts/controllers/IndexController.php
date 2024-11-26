<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Contacts
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Contacts index controller
 *
 * @category   Mage
 * @package    Mage_Contacts
 */
class Mage_Contacts_IndexController extends Mage_Core_Controller_Front_Action
{
    public const XML_PATH_ENABLED                    = 'contacts/contacts/enabled';
    public const XML_PATH_EMAIL_SENDER               = 'contacts/email/sender_email_identity';
    public const XML_PATH_EMAIL_RECIPIENT            = 'contacts/email/recipient_email';
    public const XML_PATH_EMAIL_TEMPLATE             = 'contacts/email/email_template';
    public const XML_PATH_AUTO_REPLY_ENABLED         = 'contacts/auto_reply/enabled';
    public const XML_PATH_AUTO_REPLY_EMAIL_TEMPLATE  = 'contacts/auto_reply/email_template';

    /**
     * @return $this
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getStoreConfigFlag(self::XML_PATH_ENABLED)) {
            $this->norouteAction();
        }
        return $this;
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('contactForm')
            ->setFormAction(Mage::getUrl('*/*/post', ['_secure' => $this->getRequest()->isSecure()]));

        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        if ($post) {
            $translate = Mage::getSingleton('core/translate');
            /** @var Mage_Core_Model_Translate $translate */
            $translate->setTranslateInline(false);
            try {
                $postObject = new Varien_Object();
                $postObject->setData($post);

                // check data
                $error = false;
                if (!Zend_Validate::is(trim($post['name']), 'NotEmpty')) {
                    $error = true;
                } elseif (!Zend_Validate::is(trim($post['comment']), 'NotEmpty')) {
                    $error = true;
                } elseif (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if ($error) {
                    Mage::throwException($this->__('Unable to submit your request. Please, try again later'));
                }

                // send email
                $mailTemplate = Mage::getModel('core/email_template');
                /** @var Mage_Core_Model_Email_Template $mailTemplate */
                $mailTemplate->setDesignConfig(['area' => 'frontend'])
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
                        null,
                        ['data' => $postObject]
                    );

                if (!$mailTemplate->getSentSuccess()) {
                    Mage::throwException($this->__('Unable to submit your request. Please, try again later'));
                }

                // send auto reply email to customer
                if (Mage::getStoreConfigFlag(self::XML_PATH_AUTO_REPLY_ENABLED)) {
                    $mailTemplate = Mage::getModel('core/email_template');
                    /** @var Mage_Core_Model_Email_Template $mailTemplate */
                    $mailTemplate->setDesignConfig(['area' => 'frontend'])
                        ->setReplyTo(Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT))
                        ->sendTransactional(
                            Mage::getStoreConfig(self::XML_PATH_AUTO_REPLY_EMAIL_TEMPLATE),
                            Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                            $post['email'],
                            null,
                            ['data' => $postObject]
                        );
                }

                $translate->setTranslateInline(true);
                Mage::getSingleton('customer/session')->addSuccess($this->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
            } catch (Mage_Core_Exception $e) {
                $translate->setTranslateInline(true);
                Mage::logException($e);
                Mage::getSingleton('customer/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('customer/session')->addError($this->__('Unable to submit your request. Please, try again later'));
            }
        }

        $this->_redirect('*/*/');
    }
}
