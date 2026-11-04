<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\SalesRule\Model\Resource\Coupon;

use Mage;
use Mage_SalesRule_Model_Resource_Coupon_Usage as Subject;
use Magento_Db_Adapter_Pdo_Mysql;
use OpenMage\Tests\Unit\OpenMageTest;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionProperty;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\SalesRule\Model\Resource\Coupon\UsageTrait;

final class UsageTest extends OpenMageTest
{
    use UsageTrait;

    private Magento_Db_Adapter_Pdo_Mysql&MockObject $adapter;

    private Subject $subject;

    /**
     * @var list<array{string, mixed}> every write, in order
     */
    private array $calls = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->calls = [];
        $this->adapter = $this->createMock(Magento_Db_Adapter_Pdo_Mysql::class);
        $this->adapter->method('insertOnDuplicate')->willReturnCallback(function (string $table, array $data, array $fields): int {
            $this->calls[] = ['insertOnDuplicate', ['table' => $table, 'data' => $data, 'fields' => array_map(strval(...), $fields)]];
            return 1;
        });
        $subject = Mage::getResourceModel('salesrule/coupon_usage');
        self::assertInstanceOf(Subject::class, $subject);
        $this->subject = $subject;
        (new ReflectionProperty($this->subject, '_connections'))->setValue($this->subject, ['read' => $this->adapter, 'write' => $this->adapter]);
        // resolving the real table name needs a database connection
        (new ReflectionProperty($this->subject, '_tables'))->setValue($this->subject, ['coupon_usage' => 'salesrule_coupon_usage']);
    }

    public function testIncrementWithoutLimitIsASingleUpsert(): void
    {
        $this->adapter->expects(self::never())->method('update');

        self::assertTrue($this->subject->updateCustomerCouponTimesUsed(7, 12));

        self::assertSame(
            [['insertOnDuplicate', [
                'table' => 'salesrule_coupon_usage',
                'data' => ['coupon_id' => 12, 'customer_id' => 7, 'times_used' => 1],
                'fields' => ['times_used' => 'times_used + 1'],
            ]]],
            $this->calls,
        );
    }

    public function testIncrementWithLimitCreatesTheRowBeforeTheConditionalUpdate(): void
    {
        $this->expectUpdate(1);

        self::assertTrue($this->subject->updateCustomerCouponTimesUsed(7, 12, false, 2));

        // never an UPDATE of a row that may not exist yet: under REPEATABLE READ it locks the gap
        self::assertSame(['insertOnDuplicate', 'update'], array_column($this->calls, 0));
        self::assertSame(
            [
                'table' => 'salesrule_coupon_usage',
                'data' => ['coupon_id' => 12, 'customer_id' => 7, 'times_used' => 0],
                'fields' => ['times_used' => 'times_used'],
            ],
            $this->calls[0][1],
        );
        self::assertSame(
            [
                'bind' => ['times_used' => 'times_used + 1'],
                'where' => ['coupon_id = ?' => 12, 'customer_id = ?' => 7, 'times_used < ?' => 2],
            ],
            $this->calls[1][1],
        );
    }

    public function testIncrementReportsFailureWhenTheLimitIsReached(): void
    {
        $this->expectUpdate(0);

        self::assertFalse($this->subject->updateCustomerCouponTimesUsed(7, 12, false, 2));
    }

    public function testDecrementNeverGoesBelowZero(): void
    {
        $this->expectUpdate(1);

        self::assertTrue($this->subject->updateCustomerCouponTimesUsed(7, 12, true));

        self::assertSame(
            [['update', [
                'bind' => ['times_used' => 'times_used - 1'],
                'where' => ['coupon_id = ?' => 12, 'customer_id = ?' => 7, 'times_used > ?' => 0],
            ]]],
            $this->calls,
        );
    }

    private function expectUpdate(int $affectedRows): void
    {
        $this->adapter->expects(self::once())->method('update')->willReturnCallback(
            function (string $table, array $bind, array $where) use ($affectedRows): int {
                self::assertSame('salesrule_coupon_usage', $table);
                $this->calls[] = ['update', ['bind' => array_map(strval(...), $bind), 'where' => $where]];
                return $affectedRows;
            },
        );
    }
}
