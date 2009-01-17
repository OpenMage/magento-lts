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
 * Template model
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Newsletter_Model_Template extends Varien_Object
{
    /**
     * Types of template
     */
    const TYPE_TEXT = 1;
    const TYPE_HTML = 2;


    protected $_preprocessFlag = false;
    protected $_mail;

    /**
     * Return resource of template model.
     *
     * @return Mage_Newsletter_Model_Mysql4_Template
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('newsletter/template');
    }

    /**
     * Load template by id
     *
     * @param   int $templateId
     * return   Mage_Newsletter_Model_Template
     */
    public function load($templateId)
    {
        $this->addData($this->getResource()->load($templateId));
        return $this;
    }

    /**
     * Load template by code
     *
     * @param   string $templateCode
     * return   Mage_Newsletter_Model_Template
     */
    public function loadByCode($templateCode)
    {
        $this->addData($this->getResource()->loadByCode($templateCode));
        return $this;
    }

    /**
     * Return template id
     * return int|null
     */
    public function getId()
    {
        return $this->getTemplateId();
    }

    /**
     * Set id of template
     * @param int $value
     */
    public function setId($value)
    {
        return $this->setTemplateId($value);
    }

    /**
     * Return true if this template can be used for sending queue as main template
     *
     * @return boolean
     */
    public function isValidForSend()
    {
        return !Mage::getStoreConfigFlag('system/smtp/disable')
            && $this->getTemplateSenderName() 
            && $this->getTemplateSenderEmail() 
            && $this->getTemplateSubject();
    }

    /**
     * Return true if template type eq text
     *
     * @return boolean
     */
    public function isPlain()
    {
        return $this->getTemplateType() == self::TYPE_TEXT;
    }

    /**
     * Save template
     */
    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }

    public function isPreprocessed()
    {
    	return strlen($this->getTemplateTextPreprocessed()) > 0;
    }

    public function getTemplateTextPreprocessed()
    {
    	if($this->_preprocessFlag) {
    		$this->setTemplateTextPreprocessed($this->getProcessedTemplate());
    	}

    	return $this->getData('template_text_preprocessed');
    }

    public function getProcessedTemplate(array $variables = array(), $usePreprocess=false)
    {
        $processor = new Varien_Filter_Template();

        if(!$this->_preprocessFlag) {
        	$variables['this'] = $this;
        }

        $processor
            ->setIncludeProcessor(array($this, 'getInclude'))
            ->setVariables($variables);

        if($usePreprocess && $this->isPreprocessed()) {
        	return $processor->filter($this->getTemplateTextPreprocessed());
        }

        return $processor->filter($this->getTemplateText());
    }



    public function getInclude($template, array $variables)
    {
        $thisClass = __CLASS__;
        $includeTemplate = new $thisClass();

        $includeTemplate->loadByCode($template);

        return $includeTemplate->getProcessedTemplate($variables);
    }

    /**
     * Retrieve mail object instance
     *
     * @return Zend_Mail
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
     **/
    public function send($subscriber, array $variables = array(), $name=null, Mage_Newsletter_Model_Queue $queue=null)
    {
        if(!$this->isValidForSend()) {
            return false;
        }

        $email = '';
        if($subscriber instanceof Mage_Newsletter_Model_Subscriber) {
            $email = $subscriber->getSubscriberEmail();
            if (is_null($name) && ($subscriber->hasCustomerFirstname() || $subscriber->hasCustomerLastname()) ) {
                $name = $subscriber->getCustomerFirstname() . ' ' . $subscriber->getCustomerLastname();
            }
        } else {
            $email = (string) $subscriber;
        }


        ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
        ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));

        $mail = $this->getMail();
        $mail->addTo($email, $name);
        $text = $this->getProcessedTemplate($variables, true);

        if($this->isPlain()) {
            $mail->setBodyText($text);
        } else {
            $mail->setBodyHTML($text);
        }

        $mail->setSubject($this->getProcessedTemplateSubject($variables));
        $mail->setFrom($this->getTemplateSenderEmail(), $this->getTemplateSenderName());

        try {
            $mail->send();
            $this->_mail = null;
         	if(!is_null($queue)) {
            	$subscriber->received($queue);
            }
        }
        catch (Exception $e) {
            if($subscriber instanceof Mage_Newsletter_Model_Subscriber) {
                // If letter sent for subscriber, we create a problem report entry
                $problem = Mage::getModel('newsletter/problem');
                $problem->addSubscriberData($subscriber);
                if(!is_null($queue)) {
                	$problem->addQueueData($queue);
                }
                $problem->addErrorData($e);
                $problem->save();

                if(!is_null($queue)) {
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
     * Delete template from DB
     */
    public function delete()
    {
        $this->getResource()->delete($this->getId());
        $this->setId(null);
        return $this;
    }

    public function preprocess()
    {
    	$this->_preprocessFlag = true;
    	$this->save();
    	$this->_preprocessFlag = false;
    	return $this;
    }

	public function getProcessedTemplateSubject(array $variables)
	{
		$processor = new Varien_Filter_Template();

		if(!$this->_preprocessFlag) {
			$variables['this'] = $this;
		}

		$processor->setVariables($variables);

		return $processor->filter($this->getTemplateSubject());
	}

	public function getTemplateText()
	{
	    if (!$this->getData('template_text') && !$this->getId()) {
	        $this->setData(
	           'template_text',
	           Mage::helper('newsletter')->__(
	               '<!-- This tag is for unsubscribe link  --> Follow this link to unsubscribe <a href="{{var subscriber.getUnsubscriptionLink()}}">{{var subscriber.getUnsubscriptionLink()}}</a>'
	           )
	        );
	    }

	    return $this->getData('template_text');
	}
}