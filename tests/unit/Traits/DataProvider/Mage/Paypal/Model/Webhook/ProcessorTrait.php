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
            'order approved' => ['CHECKOUT.ORDER.APPROVED', Subject::ACTION_ORDER_APPROVED],
            'order completed' => ['CHECKOUT.ORDER.COMPLETED', Subject::ACTION_ORDER_COMPLETED],
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
            'unknown event' => ['CHECKOUT.PAYMENT-APPROVAL.REVERSED', Subject::ACTION_IGNORE],
        ];
    }

    /**
     * @return array<string, array{0: string, 1: int, 2: string, 3: null|array{0: bool, 1: bool}}>
     */
    public function provideRecoveryData(): array
    {
        $pastGrace = '2026-09-14 11:54:00';
        $withinGrace = '2026-09-14 11:58:00';

        return [
            'approval still within the grace period' => ['CHECKOUT.ORDER.APPROVED', 0, $withinGrace, null],
            'approval past the grace period is charged' => ['CHECKOUT.ORDER.APPROVED', 0, $pastGrace, [true, false]],
            'capture is placed without waiting' => ['PAYMENT.CAPTURE.COMPLETED', 0, $withinGrace, [false, false]],
            'completed order is placed without waiting' => ['CHECKOUT.ORDER.COMPLETED', 0, $withinGrace, [false, false]],
            'last attempt notifies the store' => ['PAYMENT.CAPTURE.COMPLETED', 2, $withinGrace, [false, true]],
            'attempts exhausted' => ['PAYMENT.CAPTURE.COMPLETED', 3, $withinGrace, null],
            'refund never places an order' => ['PAYMENT.CAPTURE.REFUNDED', 0, $pastGrace, null],
        ];
    }
}
