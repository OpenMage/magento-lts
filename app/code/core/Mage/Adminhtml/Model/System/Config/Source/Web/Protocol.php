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
class Mage_Adminhtml_Model_System_Config_Source_Web_Protocol
{
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => ''],
            ['value' => 'http', 'label' => Mage::helper('adminhtml')->__('HTTP (unsecure)')],
            ['value' => 'https', 'label' => Mage::helper('adminhtml')->__('HTTPS (SSL)')],
        ];
    }
}
