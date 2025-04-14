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

namespace OpenMage\Tests\Unit\Mage\Admin\Model;

use Generator;
use Mage;
use Mage_Admin_Model_Variable as Subject;
use PHPUnit\Framework\TestCase;

class VariableTest extends TestCase
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = Mage::getModel('admin/variable');
    }

    /**
     * @dataProvider provideValidateAdminVariableData
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testValidate(bool|array $expectedResult, string $variableName, string $isAllowed): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods(['getVariableName', 'getIsAllowed'])
            ->getMock();

        $mock->method('getVariableName')->willReturn($variableName);
        $mock->method('getIsAllowed')->willReturn($isAllowed);
        static::assertSame($expectedResult, $mock->validate());
    }

    public function provideValidateAdminVariableData(): Generator
    {
        yield 'test passes' => [
            true,
            'test',
            '1',
        ];
        yield 'test error empty' => [
            ['Variable Name is required field.'],
            '',
            '1',
        ];
        yield 'test error regex' => [
            ['Variable Name is incorrect.'],
            '#invalid-name#',
            '1',
        ];
        yield 'test error allowed' => [
            ['Is Allowed is required field.'],
            'invalid',
            '',
        ];
    }

    public function testIsPathAllowed(): void
    {
        static::assertIsBool(self::$subject->isPathAllowed('invalid-path'));
    }
}
