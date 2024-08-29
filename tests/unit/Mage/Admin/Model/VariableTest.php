<?php

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Admin\Model;

use Mage;
use Mage_Admin_Model_Variable;
use PHPUnit\Framework\TestCase;

class VariableTest extends TestCase
{
    /**
     * @var Mage_Admin_Model_Variable
     */
    public Mage_Admin_Model_Variable $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('admin/variable');
    }

    /**
     * @dataProvider provideValidateData
     * @param array|true $expectedResult
     * @param string $variableName
     * @param string $isallowed
     * @return void
     */
    public function testValidate($expectedResult, string $variableName, string $isallowed): void
    {
        $mock = $this->getMockBuilder(Mage_Admin_Model_Variable::class)
            ->setMethods(['getVariableName', 'getIsAllowed'])
            ->getMock();

        $mock->expects($this->any())->method('getVariableName')->willReturn($variableName);
        $mock->expects($this->any())->method('getIsAllowed')->willReturn($isallowed);
        $this->assertSame($expectedResult, $mock->validate());
    }

    /**
     * @return array<string, array<int, bool|array|string>>
     */
    public function provideValidateData(): array
    {
        return [
            'test_passes' => [
                true,
                'test',
                '1'
            ],
            'test_error_empty' => [
                [0 => 'Variable Name is required field.'],
                '',
                '1'
            ],
            'test_error_regex' => [
                [0 => 'Variable Name is incorrect.'],
                '#invalid-name#',
                '1'
            ],
            'test_error_allowed' => [
                [0 => 'Is Allowed is required field.'],
                'test',
                'invalid'
            ],
        ];
    }

    public function testIsPathAllowed(): void
    {
        $this->assertIsBool($this->subject->isPathAllowed('invalid-path'));
    }
}
