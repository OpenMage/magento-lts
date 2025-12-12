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
            Level::Emergency->value => $helper->__('Emergency'),
            Level::Alert->value     => $helper->__('Alert'),
            Level::Critical->value  => $helper->__('Critical'),
            Level::Error->value     => $helper->__('Error'),
            Level::Warning->value   => $helper->__('Warning'),
            Level::Notice->value    => $helper->__('Notice'),
            Level::Info->value      => $helper->__('Informational'),
            Level::Debug->value     => $helper->__('Debug'),
        ];
    }
}
