<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Shipping
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
