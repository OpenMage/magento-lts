<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

use Mage_Core_Helper_Log as Log;
use Monolog\Level;

/**
 * Log Levels Source Model
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Log_Level
{
    public function toOptionArray()
    {
        $helper = Mage::helper('adminhtml');

        return [
            Log::getLogLevelValue(Level::Emergency) => $helper->__('Emergency'),
            Log::getLogLevelValue(Level::Alert)     => $helper->__('Alert'),
            Log::getLogLevelValue(Level::Critical)  => $helper->__('Critical'),
            Log::getLogLevelValue(Level::Error)     => $helper->__('Error'),
            Log::getLogLevelValue(Level::Warning)   => $helper->__('Warning'),
            Log::getLogLevelValue(Level::Notice)    => $helper->__('Notice'),
            Log::getLogLevelValue(Level::Info)      => $helper->__('Informational'),
            Log::getLogLevelValue(Level::Debug)     => $helper->__('Debug'),
        ];
    }
}
