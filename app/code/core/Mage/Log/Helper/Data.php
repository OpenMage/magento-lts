<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Log
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Log data helper
 *
 * @category   Mage
 * @package    Mage_Log
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Log_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_LOG_ENABLED = 'system/log/enable_log';

    /**
     * @var int
     */
    protected $_logLevel;

    /**
     * Allowed extensions that can be used to create a log file
     */
    private $_allowedFileExtensions = ['log', 'txt', 'html', 'csv'];

    /**
     * Mage_Log_Helper_Data constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->_logLevel = isset($data['log_level']) ? $data['log_level']
            : intval(Mage::getStoreConfig(self::XML_PATH_LOG_ENABLED));
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
     * @param string $file
     * @return bool
     */
    public function isLogFileExtensionValid($file)
    {
        $result = false;
        $validatedFileExtension = pathinfo($file, PATHINFO_EXTENSION);
        if ($validatedFileExtension && in_array($validatedFileExtension, $this->_allowedFileExtensions)) {
            $result = true;
        }

        return $result;
    }
}
