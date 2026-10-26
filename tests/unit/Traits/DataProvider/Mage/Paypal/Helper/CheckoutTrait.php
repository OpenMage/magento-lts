<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Helper;

use Generator;

trait CheckoutTrait
{
    public static function provideAppendNonceToUrlData(): Generator
    {
        yield 'trailing slash' => [
            'https://example.com/paypal/standard/cancel/nonce/abc123/',
            'https://example.com/paypal/standard/cancel/',
        ];
        yield 'no trailing slash' => [
            'https://example.com/paypal/payflow/cancelPayment/nonce/abc123/',
            'https://example.com/paypal/payflow/cancelPayment',
        ];
        yield 'query string' => [
            'https://example.com/index.php/paypal/standard/cancel/nonce/abc123/?___store=de&___SID=U',
            'https://example.com/index.php/paypal/standard/cancel/?___store=de&___SID=U',
        ];
        yield 'fragment' => [
            'https://example.com/paypal/hostedpro/cancel/nonce/abc123/#top',
            'https://example.com/paypal/hostedpro/cancel#top',
        ];
    }
}
