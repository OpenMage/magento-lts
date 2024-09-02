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
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Base;

use PHPUnit\Framework\TestCase;

class ClassLoadingTest extends TestCase
{
    /**
     * @dataProvider provideClassExistsData
     * @param bool $expectedResult
     * @param string $class
     * @return void
     */
    public function testClassExists(bool $expectedResult, string $class): void
    {
        $this->assertSame($expectedResult, class_exists($class));
    }

    /**
     * @return array<string, array<int, bool|string>>
     */
    public function provideClassExistsData(): array
    {
        return [
            'class exists #1' => [
                true,
                'Mage'
            ],
            'class exists #2' => [
                true,
                'Mage_Eav_Model_Entity_Increment_Numeric'
            ],
            'class not exists' => [
                false,
                'Mage_Non_Existent'
            ],
        ];
    }
}
