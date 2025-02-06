<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Shipping
 */

/**
 * Class Mage_Shipping_Model_Rate_Abstract
 *
 * @category   Mage
 * @package    Mage_Shipping
 *
 * @method string getCarrier()
 */
abstract class Mage_Shipping_Model_Rate_Abstract extends Mage_Core_Model_Abstract
{
    protected static $_instances;

    /**
     * @return Mage_Shipping_Model_Carrier_Abstract
     */
    public function getCarrierInstance()
    {
        $code = $this->getCarrier();
        if (!isset(self::$_instances[$code])) {
            self::$_instances[$code] = Mage::getModel('shipping/config')->getCarrierInstance($code);
        }
        return self::$_instances[$code];
    }
}
