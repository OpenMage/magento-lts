<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Admin\Model;

use Generator;
use Mage;
use Mage_Admin_Model_Variable as Subject;
use PHPUnit\Framework\TestCase;

class VariableTest extends TestCase
{
    public Subject $subject;

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
        $mock = $this->getMockBuilder(Subject::class)
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
            '1',
        ];
        yield 'test error empty' => [
            [0 => 'Variable Name is required field.'],
            '',
            '1',
        ];
        yield 'test error regex' => [
            [0 => 'Variable Name is incorrect.'],
            '#invalid-name#',
            '1',
        ];
        yield 'test error allowed' => [
            [0 => 'Is Allowed is required field.'],
            'test',
            'invalid',
        ];
    }

    public function testIsPathAllowed(): void
    {
        $this->assertIsBool($this->subject->isPathAllowed('invalid-path'));
    }
}
