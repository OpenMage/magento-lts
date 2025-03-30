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

use Generator;
use PHPUnit\Framework\TestCase;

class ClassLoadingTest extends TestCase
{
    /**
     * @group Base
     * @dataProvider provideClassExistsData
     */
    public function testClassExists(bool $expectedResult, string $class): void
    {
        $this->assertSame($expectedResult, class_exists($class));
    }

    public function provideClassExistsData(): Generator
    {
        yield 'class exists #1' => [
            true,
            'Mage',
        ];
        yield 'class exists #2' => [
            true,
            'Mage_Eav_Model_Entity_Increment_Numeric',
        ];
        yield 'class not exists' => [
            false,
            'Mage_Non_Existent',
        ];
    }
}
