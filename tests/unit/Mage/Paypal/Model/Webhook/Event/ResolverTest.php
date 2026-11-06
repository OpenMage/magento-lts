<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Model\Webhook\Event;

use Mage_Paypal_Model_Webhook_Event;
use Mage_Paypal_Model_Webhook_Event_Resolver as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\Webhook\Event\ResolverTrait;

final class ResolverTest extends OpenMageTest
{
    use ResolverTrait;

    /**
     * @dataProvider provideCandidateIdsData
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $eventData
     * @param string[]             $expectedResult
     */
    public function testExtractCandidateIdsReturnsUniqueWebhookAndResourceIds(
        array $payload,
        array $eventData,
        array $expectedResult
    ): void {
        $event = new Mage_Paypal_Model_Webhook_Event();
        $event->setData($eventData);

        $subject = new Subject();

        self::assertSame($expectedResult, $subject->extractCandidateIds($payload, $event));
    }

    /**
     * @dataProvider provideIncrementIdsData
     * @param array<string, mixed> $payload
     * @param string[]             $expectedResult
     */
    public function testExtractIncrementIdsReturnsInvoiceReferenceAndCustomIds(
        array $payload,
        array $expectedResult
    ): void {
        $subject = new Subject();

        self::assertSame($expectedResult, $subject->extractIncrementIds($payload));
    }
}
