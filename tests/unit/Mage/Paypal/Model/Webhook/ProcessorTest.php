<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Model\Webhook;

use Mage_Paypal_Model_Webhook_Event_Resolver;
use Mage_Paypal_Model_Webhook_Processor as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\Webhook\ProcessorTrait;

final class ProcessorTest extends OpenMageTest
{
    use ProcessorTrait;

    /**
     * @dataProvider provideEventActionData
     */
    public function testGetActionForEventType(string $eventType, string $expectedResult): void
    {
        $subject = new Subject($this->createMock(Mage_Paypal_Model_Webhook_Event_Resolver::class));

        self::assertSame($expectedResult, $subject->getActionForEventType($eventType));
    }
}
