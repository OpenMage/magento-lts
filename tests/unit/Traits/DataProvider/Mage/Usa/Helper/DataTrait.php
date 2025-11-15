<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Usa\Helper;

use Generator;
use Mage_Core_Helper_Measure_Length;
use Mage_Core_Helper_Measure_Weight;

trait DataTrait
{
    public function provideConvertMeasureWeightData(): Generator
    {
        yield 'kg to lbs' => [
            22.046226218487757,
            10,
            Mage_Core_Helper_Measure_Weight::KILOGRAM,
            Mage_Core_Helper_Measure_Weight::POUND,
        ];

        yield 'ounce to lbs' => [
            0.625,
            10,
            Mage_Core_Helper_Measure_Weight::OUNCE,
            Mage_Core_Helper_Measure_Weight::POUND,
        ];
    }

    public function provideConvertMeasureDimensionData(): Generator
    {
        yield 'm to cm' => [
            1000,
            10,
            Mage_Core_Helper_Measure_Length::STANDARD,
            Mage_Core_Helper_Measure_Length::CENTIMETER,
        ];
    }

    public function provideGetMeasureWeightNameData(): Generator
    {
        yield 'kg' => [
            'kg',
            Mage_Core_Helper_Measure_Weight::KILOGRAM,
        ];

        yield 'exception' => [
            'Unknown unit of measure (xyz).',
            'xyz',
        ];
    }

    public function provideGetMeasureDimensionNameData(): Generator
    {
        yield 'm' => [
            'm',
            Mage_Core_Helper_Measure_Length::STANDARD,
        ];

        yield 'exception' => [
            'Unknown unit of measure (xyz).',
            'xyz',
        ];
    }

    public function provideDisplayGirthValueData(): Generator
    {
        yield 'valid' => [
            true,
            'usps_1',
        ];

        yield 'invalid' => [
            false,
            'invalid',
        ];
    }
}
