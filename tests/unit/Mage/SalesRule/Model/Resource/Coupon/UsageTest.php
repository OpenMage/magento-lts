<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\SalesRule\Model\Resource\Coupon;

use PDOException;
use Throwable;
use Mage_SalesRule_Model_Resource_Coupon_Usage as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use Override;
use Zend_Db_Expr;
use Zend_Db_Statement_Exception;

final class UsageTest extends OpenMageTest
{
    /**
     * @var object records every write instead of talking to a database
     */
    private object $adapter;

    private Subject $subject;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->adapter = new class {
            public array $calls = [];

            public int $affectedRows = 1;

            public ?Throwable $insertFails = null;

            public function update($table, $bind, $where = '')
            {
                $this->calls[] = ['op' => 'update', 'bind' => $bind, 'where' => $where];
                return $this->affectedRows;
            }

            public function insert($table, $bind)
            {
                $this->calls[] = ['op' => 'insert', 'bind' => $bind];
                if ($this->insertFails) {
                    throw $this->insertFails;
                }

                return 1;
            }
        };

        $this->subject = new class ($this->adapter) extends Subject {
            private object $adapter;

            public function __construct(object $adapter)
            {
                $this->adapter = $adapter;
                parent::__construct();
            }

            #[Override]
            protected function _getWriteAdapter()
            {
                return $this->adapter;
            }

            /**
             * Resolving the real table name needs a database connection
             */
            #[Override]
            public function getMainTable()
            {
                return 'salesrule_coupon_usage';
            }
        };
    }

    public function testIncrementGuardsTheUsageLimitInTheStatement(): void
    {
        self::assertTrue($this->subject->updateCustomerCouponTimesUsed(7, 12, false, 2));

        self::assertCount(1, $this->adapter->calls);
        $update = $this->adapter->calls[0];

        self::assertSame('update', $update['op']);
        self::assertInstanceOf(Zend_Db_Expr::class, $update['bind']['times_used']);
        self::assertSame('times_used + 1', (string) $update['bind']['times_used']);
        self::assertSame(12, $update['where']['coupon_id = ?']);
        self::assertSame(7, $update['where']['customer_id = ?']);
        self::assertSame(2, $update['where']['times_used < ?']);
    }

    public function testIncrementInsertsTheFirstUse(): void
    {
        $this->adapter->affectedRows = 0;

        self::assertTrue($this->subject->updateCustomerCouponTimesUsed(7, 12, false, 2));

        self::assertSame(['update', 'insert'], array_column($this->adapter->calls, 'op'));
        self::assertSame(
            ['coupon_id' => 12, 'customer_id' => 7, 'times_used' => 1],
            $this->adapter->calls[1]['bind'],
        );
    }

    public function testIncrementReportsFailureWhenTheLimitIsReached(): void
    {
        $this->adapter->affectedRows = 0;
        $this->adapter->insertFails = $this->duplicateKeyException();

        self::assertFalse($this->subject->updateCustomerCouponTimesUsed(7, 12, false, 2));

        // the losing request of a concurrent first use retries the conditional update
        self::assertSame(['update', 'insert', 'update'], array_column($this->adapter->calls, 'op'));
    }

    public function testIncrementIsUnconditionalWithoutUsageLimit(): void
    {
        self::assertTrue($this->subject->updateCustomerCouponTimesUsed(7, 12));

        self::assertArrayNotHasKey('times_used < ?', $this->adapter->calls[0]['where']);
    }

    public function testDecrementNeverGoesBelowZero(): void
    {
        self::assertTrue($this->subject->updateCustomerCouponTimesUsed(7, 12, true));

        self::assertCount(1, $this->adapter->calls);
        $update = $this->adapter->calls[0];

        self::assertSame('times_used - 1', (string) $update['bind']['times_used']);
        self::assertSame(0, $update['where']['times_used > ?']);
    }

    public function testIncrementDoesNotSwallowOtherInsertErrors(): void
    {
        $previous = new PDOException('SQLSTATE[HY000]: General error: 1205 Lock wait timeout exceeded');
        $previous->errorInfo = ['HY000', 1205, 'Lock wait timeout exceeded'];
        $exception = new Zend_Db_Statement_Exception($previous->getMessage(), 0, $previous);

        $this->adapter->affectedRows = 0;
        $this->adapter->insertFails = $exception;

        try {
            $this->subject->updateCustomerCouponTimesUsed(7, 12, false, 2);
            self::fail('The insert error was swallowed');
        } catch (Zend_Db_Statement_Exception $caught) {
            self::assertSame($exception, $caught);
        }

        // no retry of the conditional update after an unrelated error
        self::assertSame(['update', 'insert'], array_column($this->adapter->calls, 'op'));
    }

    private function duplicateKeyException(): Zend_Db_Statement_Exception
    {
        $previous = new PDOException("SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '12-7' for key 'PRIMARY'");
        $previous->errorInfo = ['23000', 1062, "Duplicate entry '12-7' for key 'PRIMARY'"];

        return new Zend_Db_Statement_Exception($previous->getMessage(), 23000, $previous);
    }
}
