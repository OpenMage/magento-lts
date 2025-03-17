<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @dataProvider provideValidateData
 * @param array<int, string> $expectedResult
 * @group Mage_Admin
 * @group Mage_Admin_Model
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


    public function testValidate(array $expectedResult, array $methods): void
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

    public function provideValidateData(): Generator
    {
        yield 'errors' => [
            [
                0 => 'Block Name is required field.',
                1 => 'Block Name is incorrect.',
                2 => 'Is Allowed is required field.',
            ],
            [
                'getBlockName' => '',
                'getIsAllowed' => '',
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
