<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\SalesRule\Model\Resource;

use Mage_SalesRule_Model_Resource_Rule as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\SalesRule\Model\Resource\RuleTrait;
use Override;
use Varien_Db_Adapter_Pdo_Mysql;

final class RuleTest extends OpenMageTest
{
    use RuleTrait;

    /**
     * @var Varien_Db_Adapter_Pdo_Mysql records every query instead of talking to a database
     */
    private Varien_Db_Adapter_Pdo_Mysql $adapter;

    private Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adapter = new class extends Varien_Db_Adapter_Pdo_Mysql {
            /**
             * @var string[]
             */
            public array $queries = [];

            public array $result = [];

            public function __construct() {}

            #[Override]
            protected function _connect() {}

            #[Override]
            public function quote($value, $type = null)
            {
                if (is_array($value)) {
                    return implode(', ', array_map(fn($item) => $this->quote($item), $value));
                }

                return is_int($value) ? (string) $value : "'" . addslashes((string) $value) . "'";
            }

            #[Override]
            public function fetchPairs($sql, $bind = [], $fetchMode = null)
            {
                $this->queries[] = (string) $sql;
                return $this->result;
            }
        };
        $this->subject = new class ($this->adapter) extends Subject {
            private readonly Varien_Db_Adapter_Pdo_Mysql $adapter;

            public function __construct(Varien_Db_Adapter_Pdo_Mysql $adapter)
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
                return 'salesrule';
            }
        };
    }

    public function testLockRulesForUpdateUsesALockingReadInAscendingIdOrder(): void
    {
        $this->adapter->result = ['3' => '0', '5' => '7'];

        self::assertSame([3 => 0, 5 => 7], $this->subject->lockRulesForUpdate(['5', 3, 0, 5, '']));

        self::assertCount(1, $this->adapter->queries);
        self::assertSame(
            'SELECT `salesrule`.`rule_id`, `salesrule`.`times_used` FROM `salesrule`'
            . ' WHERE (rule_id IN (3, 5)) ORDER BY `rule_id` ASC FOR UPDATE',
            $this->adapter->queries[0],
        );
    }

    public function testLockRulesForUpdateOmitsRulesThatDoNotExist(): void
    {
        $this->adapter->result = ['3' => '2'];

        self::assertSame([3 => 2], $this->subject->lockRulesForUpdate([3, 4]));
    }

    public function testLockRulesForUpdateDoesNotQueryWithoutRules(): void
    {
        self::assertSame([], $this->subject->lockRulesForUpdate([0, '', null]));
        self::assertSame([], $this->adapter->queries);
    }
}
