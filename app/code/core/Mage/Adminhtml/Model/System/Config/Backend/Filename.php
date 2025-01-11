<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
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
