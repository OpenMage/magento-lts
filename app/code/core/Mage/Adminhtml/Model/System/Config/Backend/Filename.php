<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Filename extends Mage_Core_Model_Config_Data
{
    /**
     * Config path for system log file.
     */
    public const DEV_LOG_FILE_PATH = 'dev/log/file';

    /**
     * Config path for exception log file.
     */
    public const DEV_LOG_EXCEPTION_FILE_PATH = 'dev/log/exception_file';

    /**
     * Processing object before save data
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave()
    {
        $value      = $this->getValue();
        $configPath = $this->getPath();
        $value      = basename($value);

        // if dev/log setting, validate log file extension.
        if ($configPath == self::DEV_LOG_FILE_PATH || $configPath == self::DEV_LOG_EXCEPTION_FILE_PATH) {
            if (!Mage::helper('log')->isLogFileExtensionValid($value)) {
                throw Mage::exception(
                    'Mage_Core',
                    Mage::helper('adminhtml')->__('Invalid file extension used for log file. Allowed file extensions: log, txt, html, csv'),
                );
            }
        }

        $this->setValue($value);
        return $this;
    }
}
