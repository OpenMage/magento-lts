<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
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
