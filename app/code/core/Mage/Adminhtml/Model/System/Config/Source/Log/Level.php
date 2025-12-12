<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

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
            Level::Emergency->toRFC5424Level() => $helper->__('Emergency'),
            Level::Alert->toRFC5424Level()     => $helper->__('Alert'),
            Level::Critical->toRFC5424Level()  => $helper->__('Critical'),
            Level::Error->toRFC5424Level()     => $helper->__('Error'),
            Level::Warning->toRFC5424Level()   => $helper->__('Warning'),
            Level::Notice->toRFC5424Level()    => $helper->__('Notice'),
            Level::Info->toRFC5424Level()      => $helper->__('Informational'),
            Level::Debug->toRFC5424Level()     => $helper->__('Debug'),
        ];
    }
}
