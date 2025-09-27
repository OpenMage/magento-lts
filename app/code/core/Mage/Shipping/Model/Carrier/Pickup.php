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
     * @return Mage_Shipping_Model_Rate_Result|false
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $result = Mage::getModel('shipping/rate_result');

        if (!empty($rate)) {
            $method = Mage::getModel('shipping/rate_result_method');

            $method->setCarrier('pickup');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('store');
            $method->setMethodTitle(Mage::helper('shipping')->__('Store Pickup'));

            $method->setPrice(0);
            $method->setCost(0);

            $result->append($method);
        }

        return $result;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['pickup' => Mage::helper('shipping')->__('Store Pickup')];
    }
}
