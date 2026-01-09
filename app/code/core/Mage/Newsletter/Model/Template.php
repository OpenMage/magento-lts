<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Newsletter
 */

/**
 * Template model
 *
 * @package    Mage_Newsletter
 *
 * @method Mage_Newsletter_Model_Resource_Template            _getResource()
 * @method string                                             getAddedAt()
 * @method Mage_Newsletter_Model_Resource_Template_Collection getCollection()
 * @method bool                                               getIsSystem()
 * @method string                                             getModifiedAt()
 * @method Mage_Newsletter_Model_Resource_Template            getResource()
 * @method Mage_Newsletter_Model_Resource_Template_Collection getResourceCollection()
 * @method int                                                getTemplateActual()
 * @method string                                             getTemplateCode()
 * @method string                                             getTemplateSenderEmail()
 * @method string                                             getTemplateSenderName()
 * @method string                                             getTemplateStyles()
 * @method string                                             getTemplateSubject()
 * @method int                                                getTemplateType()
 * @method bool                                               hasAddedAt()
 * @method bool                                               hasTemplateActual()
 * @method $this                                              setAddedAt(string $value)
 * @method $this                                              setInlineCssFile(bool|string $value)
 * @method $this                                              setModifiedAt(string $value)
 * @method $this                                              setTemplateActual(int $value)
 * @method $this                                              setTemplateCode(string $value)
 * @method $this                                              setTemplateSenderEmail(string $value)
 * @method $this                                              setTemplateSenderName(string $value)
 * @method $this                                              setTemplateStyles(string $value)
 * @method $this                                              setTemplateSubject(string $value)
 * @method $this                                              setTemplateText(string $value)
 * @method $this                                              setTemplateTextPreprocessed(string $value)
 * @method $this                                              setTemplateType(int $value)
 */
class Mage_Newsletter_Model_Template extends Mage_Core_Model_Email_Template_Abstract
{
    /**
     * Template Text Preprocessed flag
     *
     * @var bool
     */
    protected $_preprocessFlag = false;

    /**
     * Mail object
     *
     * @var null|Zend_Mail
     */
    protected $_mail;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('newsletter/template');
    }

    /**
     * Validate Newsletter template
     *
     * @throws Mage_Core_Exception
     */
    public function validate()
    {
        $validator  = $this->getValidationHelper();
        $violations = new ArrayObject();

        $violations->append($validator->validateNotEmpty(
            value: $this->getDataUsingMethod('template_code'),
            message: "You must give a non-empty value for field 'template_code'",
        ));

        $message = "You must give a non-empty value for field 'template_type'";
        $templateType = $this->getDataUsingMethod('template_type');

        $violations->append($validator->validateNotEmpty(
            value: $templateType,
            message: $message,
        ));

        $violations->append($validator->validateType(
            value: $templateType,
            type: 'int',
            message: $message,
        ));

        $violations->append($validator->validateEmail(
            value: $this->getDataUsingMethod('template_sender_email'),
            message: "You must give a non-empty value for field 'template_sender_email'",
        ));

        $violations->append($validator->validateNotEmpty(
            value: $this->getDataUsingMethod('template_sender_name'),
            message: "You must give a non-empty value for field 'template_sender_name'",
        ));

        $errors = $validator->getErrorMessages($violations);
        if ($errors) {
            Mage::throwException(implode("\n", iterator_to_array($errors)));
        }
    }

    /**
     * Processing object before save data
     *
     * @inheritDoc
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave()
    {
        $this->validate();
        return parent::_beforeSave();
    }

    /**
     * Load template by code
     *
     * @param  string $templateCode
     * @return $this
     */
    public function loadByCode($templateCode)
    {
        $this->_getResource()->loadByCode($this, $templateCode);
        return $this;
    }

    /**
     * Return true if this template can be used for sending queue as main template
     *
     * @return bool
     * @deprecated since 1.4.0.1
     */
    public function isValidForSend()
    {
        return !Mage::getStoreConfigFlag('system/smtp/disable')
            && $this->getTemplateSenderName()
            && $this->getTemplateSenderEmail()
            && $this->getTemplateSubject();
    }

    /**
     * Getter for template type
     *
     * @return int|string
     */
    public function getType()
    {
        return $this->getTemplateType();
    }

    /**
     * Check is Preprocessed
     *
     * @return bool
     */
    public function isPreprocessed()
    {
        return $this->getTemplateTextPreprocessed() !== '';
    }

    /**
     * Check Template Text Preprocessed
     *
     * @return string
     */
    public function getTemplateTextPreprocessed()
    {
        if ($this->_preprocessFlag) {
            $this->setTemplateTextPreprocessed($this->getProcessedTemplate());
        }

        return (string) $this->getData('template_text_preprocessed');
    }

    /**
     * Retrieve processed template
     *
     * @param  bool   $usePreprocess
     * @return string
     */
    public function getProcessedTemplate(array $variables = [], $usePreprocess = false)
    {
        /** @var Mage_Newsletter_Model_Template_Filter $processor */
        $processor = Mage::helper('newsletter')->getTemplateProcessor();

        if (!$this->_preprocessFlag) {
            $variables['this'] = $this;
        }

        if (Mage::app()->isSingleStoreMode()) {
            $processor->setStoreId(Mage::app()->getStore());
        } else {
            $processor->setStoreId(Mage::app()->getRequest()->getParam('store_id'));
        }

        // Populate the variables array with store, store info, logo, etc. variables
        $variables = $this->_addEmailVariables($variables, $processor->getStoreId());

        $processor
            ->setTemplateProcessor([$this, 'getTemplateByConfigPath'])
            ->setIncludeProcessor([$this, 'getInclude'])
            ->setVariables($variables);

        // Filter the template text so that all HTML content will be present
        $result = $processor->filter($this->getTemplateText());
        // If the {{inlinecss file=""}} directive was included in the template, grab filename to use for inlining
        $this->setInlineCssFile($processor->getInlineCssFile());

        // Now that all HTML has been assembled, run email through CSS inlining process
        if ($usePreprocess && $this->isPreprocessed()) {
            $processedResult = $this->getPreparedTemplateText(true, $result);
        } else {
            $processedResult = $this->getPreparedTemplateText(false, $result);
        }

        return $processedResult;
    }

    /**
     * Makes additional text preparations for HTML templates
     *
     * @param  bool        $usePreprocess Use Preprocessed text or original text
     * @param  null|string $html
     * @return string
     */
    public function getPreparedTemplateText($usePreprocess = false, $html = null)
    {
        if ($usePreprocess) {
            $text = $this->getTemplateTextPreprocessed();
        } elseif ($html) {
            $text = $html;
        } else {
            $text = $this->getTemplateText();
        }

        if ($this->_preprocessFlag || $this->isPlain()) {
            return $text;
        }

        return $this->_applyInlineCss($text);
    }

    /**
     * Retrieve included template
     *
     * @param  string $templateCode
     * @return string
     */
    public function getInclude($templateCode, array $variables)
    {
        return Mage::getModel('newsletter/template')
            ->loadByCode($templateCode)
            ->getProcessedTemplate($variables);
    }

    /**
     * Retrieve mail object instance
     *
     * @return Zend_Mail
     * @deprecated since 1.4.0.1
     */
    public function getMail()
    {
        if (is_null($this->_mail)) {
            $this->_mail = new Zend_Mail('utf-8');
        }

        return $this->_mail;
    }

    /**
     * Send mail to subscriber
     *
     * @param  Mage_Newsletter_Model_Subscriber|string $subscriber subscriber Model or E-mail
     * @param  array                                   $variables  template variables
     * @param  null|string                             $name       receiver name (if subscriber model not specified)
     * @param  null|Mage_Newsletter_Model_Queue        $queue      queue model, used for problems reporting
     * @return bool
     * @throws Exception|Throwable
     * @deprecated since 1.4.0.1
     **/
    public function send($subscriber, array $variables = [], $name = null, ?Mage_Newsletter_Model_Queue $queue = null)
    {
        if (!$this->isValidForSend()) {
            return false;
        }

        $email = '';
        if ($subscriber instanceof Mage_Newsletter_Model_Subscriber) {
            $email = $subscriber->getSubscriberEmail();
            if (is_null($name)) {
                $name = $subscriber->getSubscriberFullName();
            }
        } else {
            $email = (string) $subscriber;
        }

        if (Mage::getStoreConfigFlag(Mage_Core_Model_Email_Template::XML_PATH_SENDING_SET_RETURN_PATH)) {
            $this->getMail()->setReturnPath($this->getTemplateSenderEmail());
        }

        ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
        ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));

        $mail = $this->getMail();
        $mail->addTo($email, $name);

        $text = $this->getProcessedTemplate($variables, true);

        if ($this->isPlain()) {
            $mail->setBodyText($text);
        } else {
            $mail->setBodyHtml($text);
        }

        $mail->setSubject($this->getProcessedTemplateSubject($variables));
        $mail->setFrom($this->getTemplateSenderEmail(), $this->getTemplateSenderName());

        try {
            $transport = new Varien_Object();

            Mage::dispatchEvent('newsletter_send_before', [
                'mail'       => $mail,
                'transport'  => $transport,
                'template'   => $this,
                'subscriber' => $subscriber,
            ]);

            if ($transport->getTransport()) {
                $mail->send($transport->getTransport());
            } else {
                $mail->send();
            }

            Mage::dispatchEvent('newsletter_send_after', [
                'to'         => $email,
                'html'       => !$this->isPlain(),
                'queue'      => $queue,
                'subject'    => $mail->getSubject(),
                'email_body' => $text,
            ]);
            $this->_mail = null;
            if (!is_null($queue)) {
                $subscriber->received($queue);
            }
        } catch (Exception $exception) {
            if ($subscriber instanceof Mage_Newsletter_Model_Subscriber) {
                // If letter sent for subscriber, we create a problem report entry
                $problem = Mage::getModel('newsletter/problem');
                $problem->addSubscriberData($subscriber);
                if (!is_null($queue)) {
                    $problem->addQueueData($queue);
                }

                $problem->addErrorData($exception);
                $problem->save();

                if (!is_null($queue)) {
                    $subscriber->received($queue);
                }
            } else {
                // Otherwise throw error to upper level
                throw $exception;
            }

            return false;
        }

        return true;
    }

    /**
     * Prepare Process (with save)
     *
     * @return $this
     * @throws Throwable
     * @deprecated since 1.4.0.1
     */
    public function preprocess()
    {
        $this->_preprocessFlag = true;
        $this->save();
        $this->_preprocessFlag = false;
        return $this;
    }

    /**
     * Retrieve processed template subject
     *
     * @return string
     * @throws Exception
     */
    public function getProcessedTemplateSubject(array $variables)
    {
        $processor = new Varien_Filter_Template();

        if (!$this->_preprocessFlag) {
            $variables['this'] = $this;
        }

        $processor->setVariables($variables);
        return $processor->filter($this->getTemplateSubject());
    }

    /**
     * Retrieve template text wrapper
     *
     * @return string
     */
    public function getTemplateText()
    {
        if (!$this->getData('template_text') && !$this->getId()) {
            $this->setData(
                'template_text',
                Mage::helper('newsletter')->__('Follow this link to unsubscribe <!-- This tag is for unsubscribe link  --><a href="{{var subscriber.getUnsubscriptionLink()}}">{{var subscriber.getUnsubscriptionLink()}}</a>'),
            );
        }

        return $this->getData('template_text');
    }
}
