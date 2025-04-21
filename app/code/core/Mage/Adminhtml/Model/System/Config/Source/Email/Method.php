<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Source for email send method
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Email_Method
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'bcc',
                'label' => Mage::helper('adminhtml')->__('Bcc'),
            ],
            [
                'value' => 'copy',
                'label' => Mage::helper('adminhtml')->__('Separate Email'),
            ],
        ];
    }
}
