<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

use PhpUnitsOfMeasure\Exception\NonNumericValue;
use PhpUnitsOfMeasure\Exception\NonStringUnitName;
use PhpUnitsOfMeasure\Exception\UnknownUnitOfMeasure;
use PhpUnitsOfMeasure\PhysicalQuantity\Mass;
use PhpUnitsOfMeasure\PhysicalQuantity\Length;

/**
 * @package    Mage_Usa
 */
class Mage_Usa_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Usa';

    /**
     * Convert weight in different measure types
     *
     * @param  float                              $value
     * @param  Mage_Core_Helper_Measure_Weight::* $sourceWeightMeasure
     * @param  Mage_Core_Helper_Measure_Weight::* $toWeightMeasure
     * @return null|float
     * @throws NonNumericValue
     * @throws NonStringUnitName
     */
    public function convertMeasureWeight($value, $sourceWeightMeasure, $toWeightMeasure)
    {
        if ($value) {
            $unitWeight = new Mass($value, $sourceWeightMeasure);
            return $unitWeight->toUnit($toWeightMeasure);
        }

        return null;
    }

    /**
     * Convert dimensions in different measure types
     *
     * @param  float                              $value
     * @param  Mage_Core_Helper_Measure_Length::* $sourceDimensionMeasure
     * @param  Mage_Core_Helper_Measure_Length::* $toDimensionMeasure
     * @return null|float
     * @throws NonNumericValue
     * @throws NonStringUnitName
     */
    public function convertMeasureDimension($value, $sourceDimensionMeasure, $toDimensionMeasure)
    {
        if ($value) {
            $unitDimension = new Length($value, $sourceDimensionMeasure);
            return $unitDimension->toUnit($toDimensionMeasure);
        }

        return null;
    }

    /**
     * Get name of measure by its type
     *
     * @param  string               $key
     * @return string
     * @throws UnknownUnitOfMeasure
     */
    public function getMeasureWeightName($key)
    {
        $unit = Mass::getUnit($key);
        return $unit->getName();
    }

    /**
     * Get name of measure by its type
     *
     * @param  string               $key
     * @return string
     * @throws UnknownUnitOfMeasure
     */
    public function getMeasureDimensionName($key)
    {
        $unit = Length::getUnit($key);
        return $unit->getName();
    }

    /**
     * Define if we need girth parameter in the package window
     *
     * @param  string $shippingMethod
     * @return bool
     */
    public function displayGirthValue($shippingMethod)
    {
        if (in_array($shippingMethod, [
            'usps_0_FCLE', // First-Class Mail Large Envelope
            'usps_1',      // Priority Mail
            'usps_2',      // Priority Mail Express Hold For Pickup
            'usps_3',      // Priority Mail Express
            'usps_4',      // Standard Post
            'usps_6',      // Media Mail
            'usps_INT_1',  // Priority Mail Express International
            'usps_INT_2',  // Priority Mail International
            'usps_INT_4',  // Global Express Guaranteed (GXG)
            'usps_INT_7',  // Global Express Guaranteed Non-Document Non-Rectangular
            'usps_INT_8',  // Priority Mail International Flat Rate Envelope
            'usps_INT_9',  // Priority Mail International Medium Flat Rate Box
            'usps_INT_10', // Priority Mail Express International Flat Rate Envelope
            'usps_INT_11', // Priority Mail International Large Flat Rate Box
            'usps_INT_12', // USPS GXG Envelopes
            'usps_INT_14', // First-Class Mail International Large Envelope
            'usps_INT_16', // Priority Mail International Small Flat Rate Box
            'usps_INT_20', // Priority Mail International Small Flat Rate Envelope
            'usps_INT_26', // Priority Mail Express International Flat Rate Boxes
        ])
        ) {
            return true;
        }

        return false;
    }

    /**
     * Validate ups type value
     *
     * @param  string $valueForCheck ups type value for check
     * @return bool
     */
    public function validateUpsType($valueForCheck)
    {
        $result = false;
        $sourceModel = Mage::getSingleton('usa/shipping_carrier_ups_source_type');
        foreach ($sourceModel->toOptionArray() as $allowedValue) {
            if (isset($allowedValue['value']) && $allowedValue['value'] == $valueForCheck) {
                $result = true;
                break;
            }
        }

        return $result;
    }
}
