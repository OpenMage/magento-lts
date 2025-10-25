<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Log\Model;

use Generator;
use Mage;
use Mage_Customer_Model_Customer;
use Mage_Log_Model_Customer as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Log\Model\CustomerTrait;

final class CustomerTest extends OpenMageTest
{
    use CustomerTrait;

    /**
     * @dataProvider loadByCustomerDataProvider
     * @group Model
     */
    public function testLoadByCustomer($input, int $expectedCustomerId): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->onlyMethods(['load'])
            ->getMock();

        $mock->expects(self::once())
            ->method('load')
            ->with($expectedCustomerId, 'customer_id')
            ->willReturnSelf();

        $result = $mock->loadByCustomer($input);

        self::assertSame($mock, $result);
    }

    public function loadByCustomerDataProvider(): Generator
    {
        $customerMock = $this->getMockBuilder(Mage_Customer_Model_Customer::class)
            ->onlyMethods(['getId'])
            ->getMock();
        $customerMock->method('getId')->willReturn(456);

        yield 'int' => [
            123,
            123,
        ];
        yield 'model' => [
            $customerMock,
            456,
        ];
    }

    /**
     * @dataProvider provideGetLoginAtTimestampData
     * @group Model
     */
    public function testGetLoginAtTimestamp(bool $expectedResult, array $methods): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        self::assertInstanceOf(Subject::class, $mock);
        if ($expectedResult) {
            self::assertIsInt($mock->getLoginAtTimestamp());
        } else {
            self::assertNull($mock->getLoginAtTimestamp());
        }
    }
}
