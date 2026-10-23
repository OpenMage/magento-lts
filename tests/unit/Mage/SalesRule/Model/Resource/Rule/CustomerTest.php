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
use Mage_SalesRule_Model_Rule_Customer;
use OpenMage\Tests\Unit\Mage\SalesRule\Model\Resource\AdapterFixture;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\SalesRule\Model\Resource\Rule\CustomerTrait;
use Override;

final class CustomerTest extends OpenMageTest
{
    use CustomerTrait;

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
                return 'salesrule_customer';
            }
        };
    }

    public function testLoadByCustomerRuleForUpdateUsesALockingRead(): void
    {
        $this->adapter->fetchRowResult = ['rule_customer_id' => '9', 'rule_id' => '3', 'customer_id' => '7', 'times_used' => '2'];
        $ruleCustomer = new Mage_SalesRule_Model_Rule_Customer(['stale' => true]);

        $this->subject->loadByCustomerRuleForUpdate($ruleCustomer, 7, 3);

        self::assertSame(
            'SELECT `salesrule_customer`.* FROM `salesrule_customer`'
            . ' WHERE (customer_id = 7) AND (rule_id = 3) FOR UPDATE',
            $this->adapter->queries[0],
        );
        self::assertSame($this->adapter->fetchRowResult, $ruleCustomer->getData());
    }

    public function testLoadByCustomerRuleForUpdateResetsTheModelWithoutARecord(): void
    {
        $ruleCustomer = new Mage_SalesRule_Model_Rule_Customer(['rule_customer_id' => 4, 'times_used' => 5]);

        $this->subject->loadByCustomerRuleForUpdate($ruleCustomer, 7, 3);

        self::assertSame([], $ruleCustomer->getData());
    }
}
