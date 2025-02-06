<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Shipping
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
