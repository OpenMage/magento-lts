<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Source for email send method
 *
 * @category   Mage
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
