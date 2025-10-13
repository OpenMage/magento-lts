<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Base;

use Generator;
use PHPUnit\Framework\TestCase;

final class ClassLoadingTest extends TestCase
{
    /**
     * @dataProvider provideClassExistsData
     */
    public function testClassExists(bool $expectedResult, string $class): void
    {
        self::assertSame($expectedResult, class_exists($class));
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
