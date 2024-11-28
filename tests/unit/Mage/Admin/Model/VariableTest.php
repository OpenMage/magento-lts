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
use Mage_Admin_Model_Variable;
use PHPUnit\Framework\TestCase;

class VariableTest extends TestCase
{
    public Mage_Admin_Model_Variable $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('admin/variable');
    }

    /**
     * @dataProvider provideValidateData
     * @group Mage_Admin
     * @group Mage_Admin_Model
     *
     * @param array|true $expectedResult
     */
    public function testValidate($expectedResult, string $variableName, string $isAllowed): void
    {
        $mock = $this->getMockBuilder(Mage_Admin_Model_Variable::class)
            ->setMethods(['getVariableName', 'getIsAllowed'])
            ->getMock();

        $mock->method('getVariableName')->willReturn($variableName);
        $mock->method('getIsAllowed')->willReturn($isAllowed);
        $this->assertSame($expectedResult, $mock->validate());
    }

    public function provideValidateData(): Generator
    {
        yield 'test passes' => [
            true,
            'test',
            '1'
        ];
        yield 'test error empty' => [
            [0 => 'Variable Name is required field.'],
            '',
            '1'
        ];
        yield 'test error regex' => [
            [0 => 'Variable Name is incorrect.'],
            '#invalid-name#',
            '1'
        ];
        yield 'test error allowed' => [
            [0 => 'Is Allowed is required field.'],
            'test',
            'invalid'
        ];
    }

    public function testIsPathAllowed(): void
    {
        $this->assertIsBool($this->subject->isPathAllowed('invalid-path'));
    }
}
