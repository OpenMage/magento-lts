<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Contacts
 */

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * Contacts index controller
 *
 * @package    Mage_Contacts
 */
class Mage_Contacts_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Use CSRF validation flag from contacts config
     */
    public const XML_CSRF_USE_FLAG_CONFIG_PATH       = 'contacts/security/enable_form_key';
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
                if (!$this->_validateFormKey()) {
                    Mage::throwException($this->__('Invalid Form Key. Please submit your request again.'));
                }

                $postObject = new Varien_Object();
                $postObject->setData($post);

                $validator  = Validation::createValidator();
                $violations = [];
                $errors = new ArrayObject();

                $violations[] = $validator->validate(trim($post['name']), [new Assert\NotBlank()]);
                $violations[] = $validator->validate(trim($post['comment']), [new Assert\NotBlank()]);
                $violations[] = $validator->validate(trim($post['email']), [new Assert\NotBlank(), new Assert\Email()]);

                foreach ($violations as $violation) {
                    foreach ($violation as $error) {
                        $errors->append($error->getMessage());
                    }
                }

                if (count($errors) !== 0) {
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
                        ['data' => $postObject],
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
                            ['data' => $postObject],
                        );
                }

                $translate->setTranslateInline(true);
                Mage::getSingleton('customer/session')->addSuccess($this->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
            } catch (Mage_Core_Exception $exception) {
                $translate->setTranslateInline(true);
                Mage::logException($exception);
                Mage::getSingleton('customer/session')->addError($exception->getMessage());
            } catch (Throwable $throwable) {
                Mage::logException($throwable);
                Mage::getSingleton('customer/session')->addError($this->__('Unable to submit your request. Please, try again later'));
            }
        }

        $this->_redirect('*/*/');
    }

    /**
     * Check if form key validation is enabled in contacts config.
     *
     * @return bool
     */
    protected function _isFormKeyEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_CSRF_USE_FLAG_CONFIG_PATH);
    }
}
