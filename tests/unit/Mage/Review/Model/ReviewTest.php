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
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Review\Model;

use Generator;
use Mage;
use Mage_Review_Model_Review as Subject;
use PHPUnit\Framework\TestCase;

class ReviewTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('review/review');
    }

    /**
     * @dataProvider provideValidateData
     * @param array|true $expectedResult
     * @group Model
     * @group Mage_Review_Model
     */
    public function testValidate($expectedResult, array $methods): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods([
                'getTitle',
                'getDetail',
                'getNickname',
                'getCustomerId',
                'getEntityId',
                'getStoreId',
            ])
            ->getMock();

        $mock->method('getTitle')->willReturn($methods['getTitle']);
        $mock->method('getDetail')->willReturn($methods['getDetail']);
        $mock->method('getNickname')->willReturn($methods['getNickname']);
        $mock->method('getCustomerId')->willReturn($methods['getCustomerId']);
        $mock->method('getEntityId')->willReturn($methods['getEntityId']);
        $mock->method('getStoreId')->willReturn($methods['getStoreId']);

        $this->assertSame($expectedResult, $mock->validate());
    }

    public function provideValidateData(): Generator
    {
        yield 'valid data' => [
            true,
            [
                'getTitle' => 'Great product',
                'getDetail' => 'I really liked this product.',
                'getNickname' => 'JohnDoe',
                'getCustomerId' => 1,
                'getEntityId' => 1,
                'getStoreId' => 1,
            ],
        ];
        yield 'missing title' => [
            ['Review summary can\'t be empty'],
            [
                'getTitle' => '',
                'getDetail' => 'I really liked this product.',
                'getNickname' => 'JohnDoe',
                'getCustomerId' => 1,
                'getEntityId' => 1,
                'getStoreId' => 1,
            ],
        ];
        yield 'missing detail' => [
            ['Review can\'t be empty'],
            [
                'getTitle' => 'Great product',
                'getDetail' => '',
                'getNickname' => 'JohnDoe',
                'getCustomerId' => 1,
                'getEntityId' => 1,
                'getStoreId' => 1,
            ],
        ];
        yield 'missing nickname' => [
            ['Nickname can\'t be empty'],
            [
                'getTitle' => 'Great product',
                'getDetail' => 'I really liked this product.',
                'getNickname' => '',
                'getCustomerId' => 1,
                'getEntityId' => 1,
                'getStoreId' => 1,
            ],
        ];
    }
}
