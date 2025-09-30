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
class Mage_Adminhtml_Model_System_Config_Source_Email_Smtpauth
{
    public function toOptionArray()
    {
        return [
            ['value' => 'NONE', 'label' => 'NONE'],
            ['value' => 'PLAIN', 'label' => 'PLAIN'],
            ['value' => 'LOGIN', 'label' => 'LOGIN'],
            ['value' => 'CRAM-MD5', 'label' => 'CRAM-MD5'],
        ];
    }
}
