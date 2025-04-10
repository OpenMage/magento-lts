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
use Mage_Admin_Model_Block as Subject;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('admin/block');
    }

    /**
     * @dataProvider provideValidateAdminBlockData
     * @param array<int, string> $expectedResult
     *
     * @group Mage_Admin
     * @group Mage_Admin_Model
     * @group Mage_Admin_Model_Test
     */
    public function testValidate($expectedResult, array $methods): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods([
                'getBlockName',
                'getIsAllowed',
            ])
            ->getMock();

        $mock->method('getBlockName')->willReturn($methods['getBlockName']);
        $mock->method('getIsAllowed')->willReturn($methods['getIsAllowed']);
        $this->assertEquals($expectedResult, $mock->validate());
    }

    public function provideValidateAdminBlockData(): Generator
    {
        $errorIncorrectBlockName = 'Block Name is incorrect.';

        yield 'valid' => [
            true,
            [
                'getBlockName' => 'test/block',
                'getIsAllowed' => '1',
            ],
        ];
        yield 'invalid' => [
            [$errorIncorrectBlockName],
            [
                'getBlockName' => 'Test_Block',
                'getIsAllowed' => '1',
            ],
        ];
        yield 'errors: blank blockname' => [
            [
                0 => 'Block Name is required field.',
                1 => 'Is Allowed is required field.',
            ],
            [
                'getBlockName' => '',
                'getIsAllowed' => '',
            ],
        ];
        yield 'errors: invalid char blockname' => [
            [$errorIncorrectBlockName],
            [
                'getBlockName' => '~',
                'getIsAllowed' => '0',
            ],
        ];
        yield 'errors: invalid blockname' => [
            [$errorIncorrectBlockName],
            [
                'getBlockName' => 'test',
                'getIsAllowed' => '0',
            ],
        ];
    }

    /**
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testIsTypeAllowed(): void
    {
        $this->assertIsBool($this->subject->isTypeAllowed('invalid-type'));
    }
}
