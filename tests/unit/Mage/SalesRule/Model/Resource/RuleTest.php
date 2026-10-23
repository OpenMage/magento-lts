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

final class RuleTest extends OpenMageTest
{
    use RuleTrait;

    private AdapterFixture $adapter;

    private Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adapter = new AdapterFixture();
        $this->subject = new class ($this->adapter) extends Subject {
            private readonly AdapterFixture $adapter;

            public function __construct(AdapterFixture $adapter)
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
        $this->adapter->fetchPairsResult = ['3' => '0', '5' => '7'];

        // rule ids read from the database or a comma separated list arrive as strings, unset ones as '' or null
        self::assertSame([3 => 0, 5 => 7], $this->subject->lockRulesForUpdate(['5', 3, 0, 5, ''])); // @phpstan-ignore argument.type

        self::assertCount(1, $this->adapter->queries);
        self::assertSame(
            'SELECT `salesrule`.`rule_id`, `salesrule`.`times_used` FROM `salesrule`'
            . ' WHERE (rule_id IN (3, 5)) ORDER BY `rule_id` ASC FOR UPDATE',
            $this->adapter->queries[0],
        );
    }

    public function testLockRulesForUpdateOmitsRulesThatDoNotExist(): void
    {
        $this->adapter->fetchPairsResult = ['3' => '2'];

        self::assertSame([3 => 2], $this->subject->lockRulesForUpdate([3, 4]));
    }

    public function testLockRulesForUpdateDoesNotQueryWithoutRules(): void
    {
        self::assertSame([], $this->subject->lockRulesForUpdate([0, '', null])); // @phpstan-ignore argument.type
        self::assertSame([], $this->adapter->queries);
    }
}
