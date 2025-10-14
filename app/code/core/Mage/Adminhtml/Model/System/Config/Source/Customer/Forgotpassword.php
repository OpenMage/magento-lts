<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Source model of forgot password flow requests options types
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Customer_Forgotpassword
{
    public const FORGOTPASS_FLOW_DISABLED  = 0;

    public const FORGOTPASS_FLOW_IP_EMAIL  = 1;

    public const FORGOTPASS_FLOW_IP        = 2;

    public const FORGOTPASS_FLOW_EMAIL     = 3;

    public function toOptionArray()
    {
        return [
            ['value' => self::FORGOTPASS_FLOW_DISABLED, 'label' => Mage::helper('adminhtml')->__('Disabled')],
            ['value' => self::FORGOTPASS_FLOW_IP_EMAIL, 'label' => Mage::helper('adminhtml')->__('By IP and Email')],
            ['value' => self::FORGOTPASS_FLOW_IP,       'label' => Mage::helper('adminhtml')->__('By IP')],
            ['value' => self::FORGOTPASS_FLOW_EMAIL,    'label' => Mage::helper('adminhtml')->__('By Email')],
        ];
    }
}
