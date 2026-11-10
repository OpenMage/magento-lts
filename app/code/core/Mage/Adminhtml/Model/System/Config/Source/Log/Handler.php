<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

use Mage_Core_Helper_Log as Log;

/**
 * Log Handlers Source Model
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Log_Handler
{
    /**
     * @return array<string, string>
     */
    public function toOptionArray()
    {
        $helper = Mage::helper('adminhtml');

        return [
            Log::HANDLER_FILE       => $helper->__('File'),
            Log::HANDLER_SYSLOG     => $helper->__('Syslog'),
            Log::HANDLER_SYSLOG_UDP => $helper->__('Syslog (UDP)'),
        ];
    }
}
