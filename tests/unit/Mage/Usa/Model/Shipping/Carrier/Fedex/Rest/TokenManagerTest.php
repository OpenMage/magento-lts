<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Usa\Model\Shipping\Carrier\Fedex\Rest;

use Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_TokenManager as TokenManager;
use OpenMage\Tests\Unit\OpenMageTest;
use Saloon\Http\Auth\AccessTokenAuthenticator;

class TokenManagerTest extends OpenMageTest
{
    public function setUp(): void
    {
        parent::setUp();
        \Mage::app()->getCache()->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, [TokenManager::CACHE_TAG]);
    }

    public function testReturnsFalseOnCacheMiss(): void
    {
        $this->assertFalse(TokenManager::get('client-miss.sandbox'));
    }

    public function testStoresAndReturnsAuthenticatorOnHit(): void
    {
        $authenticator = new AccessTokenAuthenticator(
            'access-token-value',
            null,
            new \DateTimeImmutable('+1 hour'),
        );

        TokenManager::set('client-hit.sandbox', $authenticator);

        $retrieved = TokenManager::get('client-hit.sandbox');
        $this->assertInstanceOf(AccessTokenAuthenticator::class, $retrieved);
        $this->assertSame('access-token-value', $retrieved->getAccessToken());
    }

    public function testTreatsTokenAsExpiredWithinBufferWindow(): void
    {
        $authenticator = new AccessTokenAuthenticator(
            'near-expiry',
            null,
            new \DateTimeImmutable('+' . (TokenManager::EXPIRY_BUFFER_SECONDS - 5) . ' seconds'),
        );

        TokenManager::set('client-near-expiry.sandbox', $authenticator);

        $this->assertFalse(TokenManager::get('client-near-expiry.sandbox'));
    }

    public function testSkipsWriteWhenTokenAlreadyInsideBuffer(): void
    {
        $authenticator = new AccessTokenAuthenticator(
            'already-expired',
            null,
            new \DateTimeImmutable('-5 seconds'),
        );

        TokenManager::set('client-past.sandbox', $authenticator);

        $this->assertFalse(TokenManager::get('client-past.sandbox'));
    }
}
