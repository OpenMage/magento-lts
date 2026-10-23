<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\SalesRule\Model;

use Mage_Sales_Model_Order;
use Mage_SalesRule_Model_Observer as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\SalesRule\Model\ObserverTrait;
use Varien_Event;
use Varien_Event_Observer;

final class ObserverTest extends OpenMageTest
{
    use ObserverTrait;

    public function testOrdersWithoutDiscountAreNotCounted(): void
    {
        $subject = $this->subject();
        $order = new Mage_Sales_Model_Order();
        $order->setQuoteId(5)->setDiscountAmount(0)->setAppliedRuleIds('3')->setCouponCode('SINGLE1');

        // returns before any database access
        self::assertSame($subject, $subject->sales_order_afterPlace($this->observer($order)));
        self::assertFalse($subject->isQuoteCounted($order));
    }

    public function testOnlyTheFirstOrderOfAQuoteIsCheckedAgainstTheLimits(): void
    {
        $subject = $this->subject();
        $first = (new Mage_Sales_Model_Order())->setQuoteId(5);
        $second = (new Mage_Sales_Model_Order())->setQuoteId(5);
        $otherQuote = (new Mage_Sales_Model_Order())->setQuoteId(6);

        self::assertFalse($subject->isQuoteCounted($first));

        $subject->setQuoteCounted($first);

        // multishipping places further orders of the same quote, which was validated only once
        self::assertTrue($subject->isQuoteCounted($second));
        self::assertFalse($subject->isQuoteCounted($otherQuote));
    }

    public function testOrdersWithoutQuoteAreAlwaysCheckedAgainstTheLimits(): void
    {
        $subject = $this->subject();
        $order = new Mage_Sales_Model_Order();

        $subject->setQuoteCounted($order);

        self::assertFalse($subject->isQuoteCounted($order));
    }

    private function subject(): Subject
    {
        return new class extends Subject {
            public function isQuoteCounted(Mage_Sales_Model_Order $order): bool
            {
                return $this->_isQuoteCounted($order);
            }

            public function setQuoteCounted(Mage_Sales_Model_Order $order): void
            {
                $this->_setQuoteCounted($order);
            }
        };
    }

    private function observer(Mage_Sales_Model_Order $order): Varien_Event_Observer
    {
        return new Varien_Event_Observer(['event' => new Varien_Event(['order' => $order])]);
    }
}
