<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\SalesRule\Model\Resource;

use Mage;
use Mage_SalesRule_Model_Coupon;
use Mage_SalesRule_Model_Resource_Coupon as Subject;
use Magento_Db_Adapter_Pdo_Mysql;
use OpenMage\Tests\Unit\OpenMageTest;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionMethod;
use ReflectionProperty;

final class CouponTest extends OpenMageTest
{
    private Magento_Db_Adapter_Pdo_Mysql&MockObject $adapter;

    private Subject $subject;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->adapter = $this->createMock(Magento_Db_Adapter_Pdo_Mysql::class);
        $this->adapter->method('describeTable')->willReturn([
            'coupon_id' => ['DATA_TYPE' => 'int', 'NULLABLE' => false],
            'code' => ['DATA_TYPE' => 'varchar', 'NULLABLE' => true],
            'usage_limit' => ['DATA_TYPE' => 'int', 'NULLABLE' => true],
            'times_used' => ['DATA_TYPE' => 'int', 'NULLABLE' => false],
        ]);
        $this->adapter->method('prepareColumnValue')->willReturnArgument(1);

        $subject = Mage::getResourceModel('salesrule/coupon');
        self::assertInstanceOf(Subject::class, $subject);
        $this->subject = $subject;
        (new ReflectionProperty($this->subject, '_connections'))->setValue($this->subject, ['read' => $this->adapter, 'write' => $this->adapter]);
        // resolving the real table name needs a database connection
        (new ReflectionProperty($this->subject, '_tables'))->setValue($this->subject, ['coupon' => 'salesrule_coupon']);
    }

    public function testIncrementTimesUsedGuardsTheUsageLimitInTheStatement(): void
    {
        $this->expectUpdate(1, [
            'coupon_id = ?' => 12,
            // the limit stored with the coupon is compared by the database, in the same statement
            0 => 'usage_limit IS NULL OR usage_limit = 0 OR times_used < usage_limit',
        ], 'times_used + 1');

        self::assertTrue($this->subject->incrementTimesUsed(12, true));
    }

    public function testIncrementTimesUsedReportsFailureWhenTheLimitIsReached(): void
    {
        $this->adapter->method('update')->willReturn(0);

        self::assertFalse($this->subject->incrementTimesUsed(12, true));
    }

    public function testIncrementTimesUsedIsUnconditionalWithoutLimitCheck(): void
    {
        $this->expectUpdate(1, ['coupon_id = ?' => 12], 'times_used + 1');

        self::assertTrue($this->subject->incrementTimesUsed(12));
    }

    public function testDecrementTimesUsedNeverGoesBelowZero(): void
    {
        $this->expectUpdate(1, ['coupon_id = ?' => 12, 'times_used > ?' => 0], 'times_used - 1');

        self::assertTrue($this->subject->decrementTimesUsed(12));
    }

    public function testSavingALoadedCouponDoesNotWriteBackItsUsageCounter(): void
    {
        $coupon = new Mage_SalesRule_Model_Coupon();
        $coupon->setData(['coupon_id' => 5, 'code' => 'SAVE', 'usage_limit' => 2, 'times_used' => 1]);
        $coupon->setOrigData();
        $coupon->setUsageLimit(3);

        $data = $this->prepareDataForSave($coupon);

        // concurrent redemptions may have changed the counter since the coupon was loaded
        self::assertArrayNotHasKey('times_used', $data);
        self::assertSame(3, $data['usage_limit']);
    }

    public function testAnExplicitlyChangedUsageCounterIsSaved(): void
    {
        $coupon = new Mage_SalesRule_Model_Coupon();
        $coupon->setData(['coupon_id' => 5, 'code' => 'SAVE', 'times_used' => 4]);
        $coupon->setOrigData();
        $coupon->setTimesUsed(0);

        self::assertSame(0, $this->prepareDataForSave($coupon)['times_used']);
    }

    public function testANewCouponIsSavedWithItsUsageCounter(): void
    {
        $coupon = new Mage_SalesRule_Model_Coupon();
        $coupon->setData(['code' => 'NEW', 'times_used' => 0]);

        self::assertSame(0, $this->prepareDataForSave($coupon)['times_used']);
    }

    /**
     * @param array<int|string, mixed> $where
     */
    private function expectUpdate(int $affectedRows, array $where, string $timesUsed): void
    {
        $this->adapter->expects(self::once())->method('update')->willReturnCallback(
            static function (string $table, array $bind, array $actualWhere) use ($affectedRows, $where, $timesUsed): int {
                self::assertSame('salesrule_coupon', $table);
                self::assertSame(['times_used' => $timesUsed], array_map(strval(...), $bind));
                self::assertSame($where, $actualWhere);
                return $affectedRows;
            },
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareDataForSave(Mage_SalesRule_Model_Coupon $coupon): array
    {
        return (array) (new ReflectionMethod($this->subject, '_prepareDataForSave'))->invoke($this->subject, $coupon);
    }
}
