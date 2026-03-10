<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Log
 */

/**
 * Log data helper
 *
 * @package    Mage_Log
 */
class Mage_Log_Helper_Data extends Mage_Core_Helper_Abstract
{
    public const XML_PATH_LOG_ENABLED = 'system/log/enable_log';

    protected $_moduleName = 'Mage_Log';

    /**
     * @var int
     */
    protected $_logLevel;

    /**
     * Allowed extensions that can be used to create a log file
     *
     * @var array
     */
    private $_allowedFileExtensions = ['log', 'txt', 'html', 'csv'];

    /**
     * Mage_Log_Helper_Data constructor.
     */
    public function __construct(array $data = [])
    {
        $this->_logLevel = $data['log_level'] ?? Mage::getStoreConfigAsInt(self::XML_PATH_LOG_ENABLED);
    }

    /**
     * Are visitor should be logged
     *
     * @return bool
     */
    public function isVisitorLogEnabled()
    {
        return $this->_logLevel == Mage_Log_Model_Adminhtml_System_Config_Source_Loglevel::LOG_LEVEL_VISITORS
        || $this->isLogEnabled();
    }

    /**
     * Are all events should be logged
     *
     * @return bool
     */
    public function isLogEnabled()
    {
        return $this->_logLevel == Mage_Log_Model_Adminhtml_System_Config_Source_Loglevel::LOG_LEVEL_ALL;
    }

    /**
     * Are all events should be disabled
     *
     * @return bool
     */
    public function isLogDisabled()
    {
        return $this->_logLevel == Mage_Log_Model_Adminhtml_System_Config_Source_Loglevel::LOG_LEVEL_NONE;
    }

    /**
     * Checking if file extensions is allowed. If passed then return true.
     *
     * @param  string $file
     * @return bool
     */
    public function isLogFileExtensionValid($file)
    {
        $result = false;
        $validatedFileExtension = pathinfo($file, PATHINFO_EXTENSION);
        if ($validatedFileExtension && in_array($validatedFileExtension, $this->_allowedFileExtensions)) {
            return true;
        }

        return $result;
    }
}
