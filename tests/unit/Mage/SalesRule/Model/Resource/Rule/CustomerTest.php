<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\SalesRule\Model\Resource\Rule;

use Mage_SalesRule_Model_Resource_Rule_Customer as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\SalesRule\Model\Resource\Rule\CustomerTrait;
use Override;
use Varien_Db_Adapter_Pdo_Mysql;
use Varien_Object;

final class CustomerTest extends OpenMageTest
{
    use CustomerTrait;

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

            public array|false $row = false;

            public function __construct() {}

            #[Override]
            protected function _connect() {}

            #[Override]
            public function quote($value, $type = null)
            {
                return is_int($value) ? (string) $value : "'" . addslashes((string) $value) . "'";
            }

            #[Override]
            public function fetchRow($sql, $bind = [], $fetchMode = null)
            {
                $this->queries[] = (string) $sql;
                return $this->row;
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
                return 'salesrule_customer';
            }
        };
    }

    public function testLoadByCustomerRuleForUpdateUsesALockingRead(): void
    {
        $this->adapter->row = ['rule_customer_id' => '9', 'rule_id' => '3', 'customer_id' => '7', 'times_used' => '2'];
        $ruleCustomer = new Varien_Object(['stale' => true]);

        $this->subject->loadByCustomerRuleForUpdate($ruleCustomer, '7', '3');

        self::assertSame(
            'SELECT `salesrule_customer`.* FROM `salesrule_customer`'
            . ' WHERE (customer_id = 7) AND (rule_id = 3) FOR UPDATE',
            $this->adapter->queries[0],
        );
        self::assertSame($this->adapter->row, $ruleCustomer->getData());
    }

    public function testLoadByCustomerRuleForUpdateResetsTheModelWithoutARecord(): void
    {
        $ruleCustomer = new Varien_Object(['rule_customer_id' => 4, 'times_used' => 5]);

        $this->subject->loadByCustomerRuleForUpdate($ruleCustomer, 7, 3);

        self::assertSame([], $ruleCustomer->getData());
    }
}
