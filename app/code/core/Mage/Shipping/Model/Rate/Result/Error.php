<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Shipping
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_Shipping_Model_Rate_Result_Error
 *
 * @category   Mage
 * @package    Mage_Shipping
 *
 * @method $this setCarrier(string $value)
 * @method $this setCarrierTitle(string $value)
 * @method $this setErrorMessage(string $value)
 */
class Mage_Shipping_Model_Rate_Result_Error extends Mage_Shipping_Model_Rate_Result_Abstract
{
    /**
     * @return string
     */
    public function getErrorMessage()
    {
        if (!$this->getData('error_message')) {
            $this->setData('error_message', Mage::helper('shipping')->__('This shipping method is currently unavailable. If you would like to ship using this shipping method, please contact us.'));
        }
        return $this->getData('error_message');
    }
}
