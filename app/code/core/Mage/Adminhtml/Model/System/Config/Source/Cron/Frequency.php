<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency
{
    protected static $_options;

    public const CRON_DAILY    = 'D';
    public const CRON_WEEKLY   = 'W';
    public const CRON_MONTHLY  = 'M';

    public function toOptionArray()
    {
        if (!self::$_options) {
            self::$_options = [
                [
                    'label' => Mage::helper('cron')->__('Daily'),
                    'value' => self::CRON_DAILY,
                ],
                [
                    'label' => Mage::helper('cron')->__('Weekly'),
                    'value' => self::CRON_WEEKLY,
                ],
                [
                    'label' => Mage::helper('cron')->__('Monthly'),
                    'value' => self::CRON_MONTHLY,
                ],
            ];
        }
        return self::$_options;
    }
}
