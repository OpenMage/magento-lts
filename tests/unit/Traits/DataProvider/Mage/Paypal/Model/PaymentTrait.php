<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model;

trait PaymentTrait
{
    /**
     * Scenarios for _assertRefundable().
     *
     * Each case: whether the guard should throw, the recorded captured amount
     * (null = never recorded, exercises the getTotalPaid() fallback), the
     * cumulative prior refund, the order's total paid, and the refund amount.
     *
     * @return array<string, array{bool, ?float, ?float, float, float}>
     */
    public static function provideRefundableScenarios(): array
    {
        return [
            'zero amount is rejected'           => [true, 100.0, null, 100.0, 0.0],
            'negative amount is rejected'       => [true, 100.0, null, 100.0, -5.0],
            'full refund of captured amount'    => [false, 100.0, null, 100.0, 100.0],
            'partial refund within captured'    => [false, 100.0, null, 100.0, 40.0],
            'amount above captured is rejected' => [true, 100.0, null, 100.0, 100.01],
            'fallback to total paid when ok'    => [false, null, null, 80.0, 80.0],
            'fallback to total paid exceeded'   => [true, null, null, 50.0, 60.0],
            'refund within remaining balance'   => [false, 100.0, 60.0, 100.0, 40.0],
            'refund above remaining balance'    => [true, 100.0, 60.0, 100.0, 41.0],
        ];
    }
}
