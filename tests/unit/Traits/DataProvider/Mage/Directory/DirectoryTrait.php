<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Directory;

use Generator;

trait DirectoryTrait
{
    public function provideGetCountriesWithOptionalZip(): Generator
    {
        yield 'as json' => [
            '["HK","IE","MO","PA"]',
            true,
        ];
        yield 'as array' => [
            [
                0 => 'HK',
                1 => 'IE',
                2 => 'MO',
                3 => 'PA',
            ],
            false,
        ];
    }


    public function provideGetCountriesWithStatesRequired(): Generator
    {
        yield 'as json' => [
            '["AT","CA","CH","DE","EE","ES","FI","FR","LT","LV","RO","US"]',
            true,
        ];
        yield 'as array' => [
            [
                0 => 'AT',
                1 => 'CA',
                2 => 'CH',
                3 => 'DE',
                4 => 'EE',
                5 => 'ES',
                6 => 'FI',
                7 => 'FR',
                8 => 'LT',
                9 => 'LV',
                10 => 'RO',
                11 => 'US',
            ],
            false,
        ];
    }
}
