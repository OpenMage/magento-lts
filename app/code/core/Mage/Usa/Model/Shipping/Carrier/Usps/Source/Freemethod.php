<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * USPS REST API Free Shipping Method Source Model
 *
 * Extends the Method source model to add a "None" option
 * for the free shipping method configuration.
 *
 * @category   Mage
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Usps_Source_Freemethod extends Mage_Usa_Model_Shipping_Carrier_Usps_Source_Method
{
    /**
     * Get option array with "None" option prepended
     *
     * @return array Array of options with 'value' and 'label' keys
     */
    public function toOptionArray()
    {
        $arr = parent::toOptionArray();
        array_unshift($arr, [
            'value' => '',
            'label' => Mage::helper('shipping')->__('None')
        ]);
        return $arr;
    }
}
