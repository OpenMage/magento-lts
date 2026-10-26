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
use Mage_SalesRule_Model_Resource_Rule as Subject;
use Mage_SalesRule_Model_Rule;
use Magento_Db_Adapter_Pdo_Mysql;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\SalesRule\Model\Resource\RuleTrait;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionMethod;
use ReflectionProperty;
use Varien_Db_Select;
use Zend_Db_Select;

final class RuleTest extends OpenMageTest
{
    use RuleTrait;

    private Magento_Db_Adapter_Pdo_Mysql&MockObject $adapter;

    private Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adapter = $this->createMock(Magento_Db_Adapter_Pdo_Mysql::class);
        $this->adapter->method('describeTable')->willReturn([
            'rule_id' => ['DATA_TYPE' => 'int', 'NULLABLE' => false],
            'name' => ['DATA_TYPE' => 'varchar', 'NULLABLE' => true],
            'times_used' => ['DATA_TYPE' => 'int', 'NULLABLE' => false],
        ]);
        $this->adapter->method('prepareColumnValue')->willReturnArgument(1);
        $this->adapter->method('select')->willReturnCallback(fn() => new Varien_Db_Select($this->adapter));
        $this->adapter->method('quoteInto')->willReturnCallback(
            static fn(string $text, mixed $value): string => str_replace('?', implode(', ', (array) $value), $text),
        );
        $subject = Mage::getResourceModel('salesrule/rule');
        self::assertInstanceOf(Subject::class, $subject);
        $this->subject = $subject;
        (new ReflectionProperty($this->subject, '_connections'))->setValue($this->subject, ['read' => $this->adapter, 'write' => $this->adapter]);
        // resolving the real table name needs a database connection
        (new ReflectionProperty($this->subject, '_tables'))->setValue($this->subject, ['rule' => 'salesrule']);
    }

    public function testUpdateTimesUsedCountsAUseOfEveryRuleInOneStatement(): void
    {
        $this->expectUpdate(2, ['rule_id IN (?)' => [3, 5]], 'times_used + 1');

        self::assertSame(2, $this->subject->updateTimesUsed(['5', 3, 5, 0, '']));
    }

    public function testUpdateTimesUsedNeverGoesBelowZero(): void
    {
        $this->expectUpdate(1, ['rule_id IN (?)' => [3], 'times_used >= ?' => 1], 'times_used - 1');

        self::assertSame(1, $this->subject->updateTimesUsed([3], -1));
    }

    public function testUpdateTimesUsedWithoutRulesDoesNothing(): void
    {
        $this->adapter->expects(self::never())->method('update');

        self::assertSame(0, $this->subject->updateTimesUsed([0, '']));
    }

    public function testGetUsesPerCustomerReturnsExistingRulesInIdOrder(): void
    {
        $this->adapter->expects(self::once())->method('fetchPairs')->willReturnCallback(
            static function (Varien_Db_Select $select): array {
                self::assertSame(['(rule_id IN (5, 3))'], $select->getPart(Zend_Db_Select::WHERE));
                self::assertSame([['rule_id', 'ASC']], $select->getPart(Zend_Db_Select::ORDER));
                self::assertSame(
                    [['salesrule', 'rule_id', null], ['salesrule', 'uses_per_customer', null]],
                    $select->getPart(Zend_Db_Select::COLUMNS),
                );
                return ['3' => '0', '5' => '2'];
            },
        );

        self::assertSame([3 => 0, 5 => 2], $this->subject->getUsesPerCustomer(['5', '3', '3', '']));
    }

    public function testGetUsesPerCustomerWithoutRulesDoesNotQuery(): void
    {
        $this->adapter->expects(self::never())->method('fetchPairs');

        self::assertSame([], $this->subject->getUsesPerCustomer(['', '0']));
    }

    public function testSavingALoadedRuleDoesNotWriteBackItsUsageCounter(): void
    {
        $rule = new Mage_SalesRule_Model_Rule();
        $rule->setData(['rule_id' => 3, 'name' => 'Sale', 'times_used' => 10]);
        $rule->setOrigData();
        $rule->setName('Summer sale');

        $data = $this->prepareDataForSave($rule);

        // concurrent orders may have changed the counter since the rule was loaded
        self::assertArrayNotHasKey('times_used', $data);
        self::assertSame('Summer sale', $data['name']);

        $rule->setTimesUsed(0);
        self::assertSame(0, $this->prepareDataForSave($rule)['times_used']);
    }

    /**
     * @param array<int|string, mixed> $where
     */
    private function expectUpdate(int $affectedRows, array $where, string $timesUsed): void
    {
        $this->adapter->expects(self::once())->method('update')->willReturnCallback(
            static function (string $table, array $bind, array $actualWhere) use ($affectedRows, $where, $timesUsed): int {
                self::assertSame('salesrule', $table);
                self::assertSame(['times_used' => $timesUsed], array_map(strval(...), $bind));
                self::assertSame($where, $actualWhere);
                return $affectedRows;
            },
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareDataForSave(Mage_SalesRule_Model_Rule $rule): array
    {
        return (array) (new ReflectionMethod($this->subject, '_prepareDataForSave'))->invoke($this->subject, $rule);
    }
}
