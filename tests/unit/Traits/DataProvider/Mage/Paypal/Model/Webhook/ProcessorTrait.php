<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\Webhook;

use Mage_Paypal_Model_Webhook_Processor as Subject;

trait ProcessorTrait
{
    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public function provideEventActionData(): array
    {
        return [
            'capture completed' => ['PAYMENT.CAPTURE.COMPLETED', Subject::ACTION_CAPTURE_COMPLETED],
            'capture denied' => ['PAYMENT.CAPTURE.DENIED', Subject::ACTION_CAPTURE_DENIED],
            'capture refunded' => ['PAYMENT.CAPTURE.REFUNDED', Subject::ACTION_CAPTURE_REFUNDED],
            'capture reversed' => ['PAYMENT.CAPTURE.REVERSED', Subject::ACTION_CAPTURE_REVERSED],
            'authorization voided' => ['PAYMENT.AUTHORIZATION.VOIDED', Subject::ACTION_AUTHORIZATION_VOIDED],
            'risk dispute created' => ['RISK.DISPUTE.CREATED', Subject::ACTION_DISPUTE],
            'customer dispute created' => ['CUSTOMER.DISPUTE.CREATED', Subject::ACTION_DISPUTE],
            'customer dispute updated' => ['CUSTOMER.DISPUTE.UPDATED', Subject::ACTION_DISPUTE],
            'customer dispute resolved' => ['CUSTOMER.DISPUTE.RESOLVED', Subject::ACTION_DISPUTE],
            'subscription event' => ['BILLING.SUBSCRIPTION.ACTIVATED', Subject::ACTION_SUBSCRIPTION],
            'unknown event' => ['CHECKOUT.ORDER.APPROVED', Subject::ACTION_IGNORE],
        ];
    }
}
