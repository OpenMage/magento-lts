<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Usa\Helper;

use Generator;

trait DataTrait
{
    public function provideConvertMeasureWeightData(): Generator
    {
        yield 'kg to lbs' => [
            '22.046',
            10,
            'KILOGRAM',
            'POUND',
        ];
    }

    public function provideConvertMeasureDimensionData(): Generator
    {
        yield 'm to cm' => [
            '1000',
            10,
            'METER',
            'CENTIMETER',
        ];
    }

    public function provideGetMeasureWeightNameData(): Generator
    {
        yield 'kg' => [
            'kg',
            'KILOGRAM',
        ];
    }

    public function provideGetMeasureDimensionNameData(): Generator
    {
        yield 'm to cm' => [
            'm',
            'METER',
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
