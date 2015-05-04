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
 * @package     Mage_Newsletter
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Template model
 *
 * @method Mage_Newsletter_Model_Resource_Template _getResource()
 * @method Mage_Newsletter_Model_Resource_Template getResource()
 * @method string getTemplateCode()
 * @method Mage_Newsletter_Model_Template setTemplateCode(string $value)
 * @method Mage_Newsletter_Model_Template setTemplateText(string $value)
 * @method Mage_Newsletter_Model_Template setTemplateTextPreprocessed(string $value)
 * @method string getTemplateStyles()
 * @method Mage_Newsletter_Model_Template setTemplateStyles(string $value)
 * @method int getTemplateType()
 * @method Mage_Newsletter_Model_Template setTemplateType(int $value)
 * @method string getTemplateSubject()
 * @method Mage_Newsletter_Model_Template setTemplateSubject(string $value)
 * @method string getTemplateSenderName()
 * @method Mage_Newsletter_Model_Template setTemplateSenderName(string $value)
 * @method string getTemplateSenderEmail()
 * @method Mage_Newsletter_Model_Template setTemplateSenderEmail(string $value)
 * @method int getTemplateActual()
 * @method Mage_Newsletter_Model_Template setTemplateActual(int $value)
 * @method string getAddedAt()
 * @method Mage_Newsletter_Model_Template setAddedAt(string $value)
 * @method string getModifiedAt()
 * @method Mage_Newsletter_Model_Template setModifiedAt(string $value)
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
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
     * @var Zend_Mail
     */
    protected $_mail;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('newsletter/template');
    }

    /**
     * Validate Newsletter template
     *
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function validate()
    {
        $validators = array(
            'template_code'         => array(Zend_Filter_Input::ALLOW_EMPTY => false),
            'template_type'         => 'Int',
            'template_sender_email' => 'EmailAddress',
            'template_sender_name'  => array(Zend_Filter_Input::ALLOW_EMPTY => false)
        );
        $data = array();
        foreach (array_keys($validators) as $validateField) {
            $data[$validateField] = $this->getDataUsingMethod($validateField);
        }

        $validateInput = new Zend_Filter_Input(array(), $validators, $data);
        if (!$validateInput->isValid()) {
            $errorMessages = array();
            foreach ($validateInput->getMessages() as $messages) {
                if (is_array($messages)) {
                    foreach ($messages as $message) {
                        $errorMessages[] = $message;
                    }
                }
                else {
                    $errorMessages[] = $messages;
                }
            }

            Mage::throwException(join("\n", $errorMessages));
        }
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Newsletter_Model_Template
     */
    protected function _beforeSave()
    {
        $this->validate();
        return parent::_beforeSave();
    }

    /**
     * Load template by code
     *
     * @param string $templateCode
     * @return Mage_Newsletter_Model_Template
     */
    public function loadByCode($templateCode)
    {
        $this->_getResource()->loadByCode($this, $templateCode);
        return $this;
    }

    /**
     * Return true if this template can be used for sending queue as main template
     *
     * @return boolean
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
    public function getType(){
        return $this->getTemplateType();
    }

    /**
     * Check is Preprocessed
     *
     * @return bool
     */
    public function isPreprocessed()
    {
        return strlen($this->getTemplateTextPreprocessed()) > 0;
    }

    /**
     * Check Template Text Preprocessed
     *
     * @return bool
     */
    public function getTemplateTextPreprocessed()
    {
        if ($this->_preprocessFlag) {
            $this->setTemplateTextPreprocessed($this->getProcessedTemplate());
        }

        return $this->getData('template_text_preprocessed');
    }

    /**
     * Retrieve processed template
     *
     * @param array $variables
     * @param bool $usePreprocess
     * @return string
     */
    public function getProcessedTemplate(array $variables = array(), $usePreprocess = false)
    {
        /* @var $processor Mage_Newsletter_Model_Template_Filter */
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
            ->setTemplateProcessor(array($this, 'getTemplateByConfigPath'))
            ->setIncludeProcessor(array($this, 'getInclude'))
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
     * @param bool $usePreprocess Use Preprocessed text or original text
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
     * @param string $templateCode
     * @param array $variables
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
     * @param   Mage_Newsletter_Model_Subscriber|string   $subscriber   subscriber Model or E-mail
     * @param   array                                     $variables    template variables
     * @param   string|null                               $name         receiver name (if subscriber model not specified)
     * @param   Mage_Newsletter_Model_Queue|null          $queue        queue model, used for problems reporting.
     * @return boolean
     * @deprecated since 1.4.0.1
     **/
    public function send($subscriber, array $variables = array(), $name=null, Mage_Newsletter_Model_Queue $queue=null)
    {
        if (!$this->isValidForSend()) {
            return false;
        }

        $email = '';
        if ($subscriber instanceof Mage_Newsletter_Model_Subscriber) {
            $email = $subscriber->getSubscriberEmail();
            if (is_null($name) && ($subscriber->hasCustomerFirstname() || $subscriber->hasCustomerLastname()) ) {
                $name = $subscriber->getCustomerFirstname() . ' ' . $subscriber->getCustomerLastname();
            }
        }
        else {
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
        }
        else {
            $mail->setBodyHTML($text);
        }

        $mail->setSubject($this->getProcessedTemplateSubject($variables));
        $mail->setFrom($this->getTemplateSenderEmail(), $this->getTemplateSenderName());

        try {
            $mail->send();
            $this->_mail = null;
            if (!is_null($queue)) {
                $subscriber->received($queue);
            }
        }
        catch (Exception $e) {
            if ($subscriber instanceof Mage_Newsletter_Model_Subscriber) {
                // If letter sent for subscriber, we create a problem report entry
                $problem = Mage::getModel('newsletter/problem');
                $problem->addSubscriberData($subscriber);
                if (!is_null($queue)) {
                    $problem->addQueueData($queue);
                }
                $problem->addErrorData($e);
                $problem->save();

                if (!is_null($queue)) {
                    $subscriber->received($queue);
                }
            } else {
                // Otherwise throw error to upper level
                throw $e;
            }
            return false;
        }

        return true;
    }

    /**
     * Prepare Process (with save)
     *
     * @return Mage_Newsletter_Model_Template
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
     * @param array $variables
     * @return string
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
            $this->setData('template_text',
                Mage::helper('newsletter')->__('Follow this link to unsubscribe <!-- This tag is for unsubscribe link  --><a href="{{var subscriber.getUnsubscriptionLink()}}">{{var subscriber.getUnsubscriptionLink()}}</a>')
            );
        }

        return $this->getData('template_text');
    }
}
