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
class Mage_Shipping_Model_Tracking_Result_Error extends Mage_Shipping_Model_Tracking_Result_Abstract
{
    /**
     * @return array
     */
    public function getAllData()
    {
        return $this->_data;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return  Mage::helper('shipping')->__('Tracking information is currently unavailable.');
    }
}
