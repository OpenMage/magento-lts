<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Model;

use Mage;
use Mage_Core_Exception;
use Mage_Paypal_Model_Observer as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\ObserverTrait;
use Varien_Event;
use Varien_Event_Observer;

final class ObserverTest extends OpenMageTest
{
    use ObserverTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('paypal/observer');
    }

    /**
     * @dataProvider provideSubmittableQuotes
     * @param array<string, mixed> $additionalInformation
     * @group Model
     */
    public function testRequirePaypalOrderReferenceLetsChargedOrOtherQuotesThrough(string $method, array $additionalInformation): void
    {
        $this->expectNotToPerformAssertions();

        self::$subject->requirePaypalOrderReference($this->buildObserver($method, $additionalInformation));
    }

    /**
     * @dataProvider provideUnpaidPaypalQuotes
     * @param array<string, mixed> $additionalInformation
     * @group Model
     */
    public function testRequirePaypalOrderReferenceRefusesUnpaidPaypalQuotes(array $additionalInformation): void
    {
        $this->expectException(Mage_Core_Exception::class);

        self::$subject->requirePaypalOrderReference($this->buildObserver('paypal', $additionalInformation));
    }

    /**
     * @param array<string, mixed> $additionalInformation
     */
    private function buildObserver(string $method, array $additionalInformation): Varien_Event_Observer
    {
        $quote = Mage::getModel('sales/quote');
        $payment = $quote->getPayment()->setMethod($method);
        foreach ($additionalInformation as $key => $value) {
            $payment->setAdditionalInformation($key, $value);
        }

        return new Varien_Event_Observer(['event' => new Varien_Event(['quote' => $quote])]);
    }
}
