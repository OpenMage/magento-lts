<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Shipping
 */

/**
 * @package    Mage_Shipping
 */
class Mage_Shipping_Model_Carrier_Pickup extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'pickup';

    protected $_isFixed = true;

    /**
     * @return false|Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        return Mage::getModel('shipping/rate_result');
    }

    /**
     * Get allowed shipping methods
     *
     * @return array<string, string>
     */
    public function getAllowedMethods()
    {
        return ['pickup' => Mage::helper('shipping')->__('Store Pickup')];
    }
}
