<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Email Template Mailer Model
 *
 * @package    Mage_Core
 *
 * @method Mage_Core_Model_Email_Queue getQueue()
 * @method $this setQueue(Mage_Core_Model_Abstract $value)
 */
class Mage_Core_Model_Email_Template_Mailer extends Varien_Object
{
    /**
     * List of email infos
     * @see Mage_Core_Model_Email_Info
     *
     * @var array
     */
    protected $_emailInfos = [];

    /**
     * Add new email info to corresponding list
     *
     * @return $this
     */
    public function addEmailInfo(Mage_Core_Model_Email_Info $emailInfo)
    {
        $this->_emailInfos[] = $emailInfo;
        return $this;
    }

    /**
     * Send all emails from email list
     *
     * @return $this
     * @see self::$_emailInfos
     */
    public function send()
    {
        /** @var Mage_Core_Model_Email_Template $emailTemplate */
        $emailTemplate = Mage::getModel('core/email_template');
        // Send all emails from corresponding list
        while (!empty($this->_emailInfos)) {
            $emailInfo = array_pop($this->_emailInfos);
            // Handle "Bcc" recipients of the current email
            $emailTemplate->addBcc($emailInfo->getBccEmails());
            // Set required design parameters and delegate email sending to Mage_Core_Model_Email_Template
            $emailTemplate->setDesignConfig(['area' => 'frontend', 'store' => $this->getStoreId()])
                ->setQueue($this->getQueue())
                ->sendTransactional(
                    $this->getTemplateId(),
                    $this->getSender(),
                    $emailInfo->getToEmails(),
                    $emailInfo->getToNames(),
                    $this->getTemplateParams(),
                    $this->getStoreId(),
                );
        }

        return $this;
    }

    /**
     * Set email sender
     *
     * @param array|string $sender
     * @return $this
     */
    public function setSender($sender)
    {
        return $this->setData('sender', $sender);
    }

    /**
     * Get email sender
     *
     * @return null|array|string
     */
    public function getSender()
    {
        return $this->_getData('sender');
    }

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData('store_id', $storeId);
    }

    /**
     * Get store id
     *
     * @return null|int
     */
    public function getStoreId()
    {
        return $this->_getData('store_id');
    }

    /**
     * Set template id
     *
     * @param int $templateId
     * @return $this
     */
    public function setTemplateId($templateId)
    {
        return $this->setData('template_id', $templateId);
    }

    /**
     * Get template id
     *
     * @return null|int
     */
    public function getTemplateId()
    {
        return $this->_getData('template_id');
    }

    /**
     * Set template parameters
     *
     * @return $this
     */
    public function setTemplateParams(array $templateParams)
    {
        return $this->setData('template_params', $templateParams);
    }

    /**
     * Get template parameters
     *
     * @return null|array
     */
    public function getTemplateParams()
    {
        return $this->_getData('template_params');
    }
}
