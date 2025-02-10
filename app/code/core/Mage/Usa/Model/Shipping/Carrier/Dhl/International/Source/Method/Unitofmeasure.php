<?php
/**
 * Source model for DHL shipping methods for documentation
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Dhl_International_Source_Method_Unitofmeasure
{
    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        $unitArr = Mage::getSingleton('usa/shipping_carrier_dhl_international')->getCode('unit_of_measure');

        $returnArr = [];
        foreach ($unitArr as $key => $val) {
            $returnArr[] = ['value' => $key, 'label' => $val];
        }
        return $returnArr;
    }
}
