<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Source model of forgot password flow requests options types
 *
 * @category   Mage
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
