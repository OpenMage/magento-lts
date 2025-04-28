<?php

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
                'Mage',
            ],
            'class exists #2' => [
                true,
                'Mage_Eav_Model_Entity_Increment_Numeric',
            ],
            'class not exists' => [
                false,
                'Mage_Non_Existent',
            ],
        ];
    }
}
