<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Log
 */

/**
 * Log Cron Model
 *
 * @package    Mage_Log
 */
class Mage_Log_Model_Cron extends Mage_Core_Model_Abstract
{
    public const XML_PATH_EMAIL_LOG_CLEAN_TEMPLATE     = 'system/log/error_email_template';

    public const XML_PATH_EMAIL_LOG_CLEAN_IDENTITY     = 'system/log/error_email_identity';

    public const XML_PATH_EMAIL_LOG_CLEAN_RECIPIENT    = 'system/log/error_email';

    public const XML_PATH_LOG_CLEAN_ENABLED            = 'system/log/enabled';

    /**
     * Error messages
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * Send Log Clean Warnings
     *
     * @return $this
     */
    protected function _sendLogCleanEmail()
    {
        if (!$this->_errors) {
            return $this;
        }

        if (!Mage::getStoreConfig(self::XML_PATH_EMAIL_LOG_CLEAN_RECIPIENT)) {
            return $this;
        }

        $translate = Mage::getSingleton('core/translate');
        /** @var Mage_Core_Model_Translate $translate */
        $translate->setTranslateInline(false);

        $emailTemplate = Mage::getModel('core/email_template');
        /** @var Mage_Core_Model_Email_Template $emailTemplate */
        $emailTemplate->setDesignConfig(['area' => 'backend'])
            ->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_EMAIL_LOG_CLEAN_TEMPLATE),
                Mage::getStoreConfig(self::XML_PATH_EMAIL_LOG_CLEAN_IDENTITY),
                Mage::getStoreConfig(self::XML_PATH_EMAIL_LOG_CLEAN_RECIPIENT),
                null,
                ['warnings' => implode("\n", $this->_errors)],
            );

        $translate->setTranslateInline(true);

        return $this;
    }

    /**
     * Clean logs
     *
     * @return $this
     */
    public function logClean()
    {
        if (!Mage::getStoreConfigFlag(self::XML_PATH_LOG_CLEAN_ENABLED)) {
            return $this;
        }

        $this->_errors = [];

        try {
            Mage::getModel('log/log')->clean();
        } catch (Exception $exception) {
            $this->_errors[] = $exception->getMessage();
            $this->_errors[] = $exception->getTrace();
        }

        $this->_sendLogCleanEmail();

        return $this;
    }
}
