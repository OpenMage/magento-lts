<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model;

trait HelperTrait
{
    /**
     * Scenarios for extractCaptureId() / extractCaptureAmount().
     *
     * `kind` selects the response shape the test builds; `amount` is the
     * capture amount string when one is present.
     *
     * @return array<string, array{string, ?string}>
     */
    public static function provideCaptureResultShapes(): array
    {
        return [
            'null result'              => ['null', null],
            'scalar result'            => ['string', null],
            'object without getters'   => ['plain', null],
            'no purchase units'        => ['emptyUnits', null],
            'purchase unit no payments' => ['noPayments', null],
            'no captures'              => ['emptyCaptures', null],
            'capture without amount'   => ['noAmount', null],
            'complete capture'         => ['ok', '42.17'],
        ];
    }
}
