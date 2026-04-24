<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

use Carbon\Carbon;
use Saloon\Http\Auth\AccessTokenAuthenticator;
use ShipStream\FedEx\Contracts\TokenCache;

class Mage_Usa_Model_Shipping_Carrier_Fedex_Rest_Tokenmanager implements TokenCache
{
    public const CACHE_KEY_PREFIX = 'fedex_oauth_';

    public const CACHE_TAG = 'FEDEX_OAUTH';

    public const EXPIRY_BUFFER_SECONDS = 60;

    public static function get(string $key): AccessTokenAuthenticator|false
    {
        try {
            $authenticator = self::hydrateAuthenticator(Mage::app()->getCache(), self::cacheKey($key));
        } catch (Throwable) {
            return false;
        }

        return $authenticator instanceof AccessTokenAuthenticator && !self::isExpiringSoon($authenticator) ? $authenticator : false;
    }

    public static function set(string $key, AccessTokenAuthenticator $authenticator): void
    {
        $ttl = self::ttlSeconds($authenticator);
        if ($ttl <= 0) {
            return;
        }

        Mage::app()->getCache()->save(
            serialize(self::normalize($authenticator)),
            self::cacheKey($key),
            [self::CACHE_TAG],
            $ttl,
        );
    }

    private static function normalize(AccessTokenAuthenticator $authenticator): AccessTokenAuthenticator
    {
        $expiresAt = $authenticator->expiresAt instanceof DateTimeImmutable
            ? DateTimeImmutable::createFromInterface($authenticator->expiresAt)
            : null;

        return new AccessTokenAuthenticator(
            $authenticator->accessToken,
            $authenticator->refreshToken,
            $expiresAt,
        );
    }

    private static function cacheKey(string $key): string
    {
        return self::CACHE_KEY_PREFIX . preg_replace('/[^A-Za-z0-9_\-]/', '_', $key);
    }

    private static function isExpiringSoon(AccessTokenAuthenticator $authenticator): bool
    {
        if (!$authenticator->expiresAt instanceof DateTimeImmutable) {
            return false;
        }

        return $authenticator->expiresAt->getTimestamp() - self::EXPIRY_BUFFER_SECONDS <= Carbon::now()->getTimestamp();
    }

    private static function ttlSeconds(AccessTokenAuthenticator $authenticator): int
    {
        if (!$authenticator->expiresAt instanceof DateTimeImmutable) {
            return 0;
        }

        return max(0, $authenticator->expiresAt->getTimestamp() - self::EXPIRY_BUFFER_SECONDS - Carbon::now()->getTimestamp());
    }

    private static function hydrateAuthenticator(Zend_Cache_Core $cache, string $key): ?AccessTokenAuthenticator
    {
        $cachedAuth = $cache->load($key);

        if (!is_string($cachedAuth) || $cachedAuth === '') {
            return null;
        }

        $authenticator = unserialize($cachedAuth, ['allowed_classes' => [
            AccessTokenAuthenticator::class,
            DateTimeImmutable::class,
            DateTimeZone::class,
        ]]);

        return $authenticator instanceof AccessTokenAuthenticator ? $authenticator : null;
    }
}
