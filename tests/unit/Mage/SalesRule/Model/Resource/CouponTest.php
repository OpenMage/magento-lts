<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\SalesRule\Model\Resource;

use Mage_SalesRule_Model_Resource_Coupon as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use Override;
use Zend_Db_Expr;

final class CouponTest extends OpenMageTest
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
            public array $updates = [];

            public int $affectedRows = 1;

            public function update($table, $bind, $where = '')
            {
                $this->updates[] = ['table' => $table, 'bind' => $bind, 'where' => $where];
                return $this->affectedRows;
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
                return 'salesrule_coupon';
            }
        };
    }

    public function testIncrementTimesUsedGuardsTheUsageLimitInTheStatement(): void
    {
        self::assertTrue($this->subject->incrementTimesUsed(12, 3));

        self::assertCount(1, $this->adapter->updates);
        $update = $this->adapter->updates[0];

        self::assertInstanceOf(Zend_Db_Expr::class, $update['bind']['times_used']);
        self::assertSame('times_used + 1', (string) $update['bind']['times_used']);
        self::assertSame(12, $update['where']['coupon_id = ?']);
        self::assertArrayHasKey('times_used < ?', $update['where']);
        self::assertSame(3, $update['where']['times_used < ?']);
    }

    public function testIncrementTimesUsedReportsFailureWhenTheLimitIsReached(): void
    {
        $this->adapter->affectedRows = 0;

        self::assertFalse($this->subject->incrementTimesUsed(12, 3));
    }

    public function testIncrementTimesUsedIsUnconditionalWithoutUsageLimit(): void
    {
        self::assertTrue($this->subject->incrementTimesUsed(12));

        self::assertArrayNotHasKey('times_used < ?', $this->adapter->updates[0]['where']);
    }

    public function testDecrementTimesUsedNeverGoesBelowZero(): void
    {
        self::assertTrue($this->subject->decrementTimesUsed(12));

        $update = $this->adapter->updates[0];

        self::assertSame('times_used - 1', (string) $update['bind']['times_used']);
        self::assertSame(12, $update['where']['coupon_id = ?']);
        self::assertSame(0, $update['where']['times_used > ?']);
    }
}
