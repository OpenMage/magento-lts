<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

namespace OpenMage\Tests\Unit\Mage\Usa\Model\Shipping\Carrier\Fedex\Rest;

use Mage;
use Zend_Cache;
use Carbon\CarbonImmutable;
use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Tokenmanager as Tokenmanager;
use OpenMage\Tests\Unit\OpenMageTest;
use Saloon\Http\Auth\AccessTokenAuthenticator;

final class TokenmanagerTest extends OpenMageTest
{
    protected function setUp(): void
    {
        parent::setUp();
        Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, [Tokenmanager::CACHE_TAG]);
    }

    public function testReturnsFalseOnCacheMiss(): void
    {
        self::assertFalse(Tokenmanager::get('client-miss.sandbox'));
    }

    public function testStoresAndReturnsAuthenticatorOnHit(): void
    {
        $authenticator = new AccessTokenAuthenticator(
            'access-token-value',
            null,
            CarbonImmutable::now()->addHours(1),
        );

        Tokenmanager::set('client-hit.sandbox', $authenticator);

        $retrieved = Tokenmanager::get('client-hit.sandbox');
        self::assertInstanceOf(AccessTokenAuthenticator::class, $retrieved);
        self::assertSame('access-token-value', $retrieved->getAccessToken());
    }

    public function testTreatsTokenAsExpiredWithinBufferWindow(): void
    {
        $authenticator = new AccessTokenAuthenticator(
            'near-expiry',
            null,
            CarbonImmutable::now()->addSeconds(Tokenmanager::EXPIRY_BUFFER_SECONDS - 5),
        );

        Tokenmanager::set('client-near-expiry.sandbox', $authenticator);

        self::assertFalse(Tokenmanager::get('client-near-expiry.sandbox'));
    }

    public function testSkipsWriteWhenTokenAlreadyInsideBuffer(): void
    {
        $authenticator = new AccessTokenAuthenticator(
            'already-expired',
            null,
            CarbonImmutable::now()->subSeconds(5),
        );

        Tokenmanager::set('client-past.sandbox', $authenticator);

        self::assertFalse(Tokenmanager::get('client-past.sandbox'));
    }
}
