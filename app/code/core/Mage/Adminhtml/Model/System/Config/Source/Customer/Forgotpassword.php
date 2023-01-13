<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Source model of forgot password flow requests options types
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
