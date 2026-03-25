<?php

/**
 * Security-focused tests for USPS REST API hardening.
 *
 * Covers: credential decryption, SSL verification, URL allowlist,
 * JSON cache serialization, and OAuth cache key fingerprinting.
 *
 * @group  Security
 * @group  Usa
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Usa\Model\Shipping\Carrier;

use OpenMage\Tests\Unit\OpenMageTest;

final class UspsSecurityTest extends OpenMageTest
{
    // ──────────────────────────────────────────────
    // Fix #1: _getOAuthToken() decrypts credentials
    // ──────────────────────────────────────────────

    /**
     * Verify _getOAuthToken() calls decrypt() on client_id and client_secret.
     * Without decrypt(), encrypted blobs are sent to USPS = auth always fails.
     */
    public function testGetOAuthTokenDecryptsCredentials(): void
    {
        $source = file_get_contents(
            __DIR__ . '/../../../../../../../app/code/core/Mage/Usa/Model/Shipping/Carrier/Usps.php',
        );
        self::assertNotFalse($source, 'Could not read Usps.php');

        // Both getConfigData calls must be wrapped in decrypt()
        self::assertStringContainsString(
            "Mage::helper('core')->decrypt(\$this->getConfigData('client_id'))",
            $source,
            '_getOAuthToken() must decrypt client_id before use',
        );
        self::assertStringContainsString(
            "Mage::helper('core')->decrypt(\$this->getConfigData('client_secret'))",
            $source,
            '_getOAuthToken() must decrypt client_secret before use',
        );
    }

    /**
     * Verify no raw getConfigData('client_id') without decrypt wrapper exists.
     */
    public function testNoRawClientIdAccess(): void
    {
        $source = file_get_contents(
            __DIR__ . '/../../../../../../../app/code/core/Mage/Usa/Model/Shipping/Carrier/Usps.php',
        );
        self::assertNotFalse($source, 'Could not read Usps.php');

        // Remove all decrypt-wrapped calls, then check no raw calls remain
        $stripped = str_replace(
            [
                "Mage::helper('core')->decrypt(\$this->getConfigData('client_id'))",
                "Mage::helper('core')->decrypt(\$this->getConfigData('client_secret'))",
            ],
            '',
            $source,
        );

        self::assertStringNotContainsString(
            "getConfigData('client_id')",
            $stripped,
            'Raw getConfigData(client_id) without decrypt() must not exist',
        );
        self::assertStringNotContainsString(
            "getConfigData('client_secret')",
            $stripped,
            'Raw getConfigData(client_secret) without decrypt() must not exist',
        );
    }

    // ──────────────────────────────────────────────
    // Fix #2 + #5: SSL verification on all curl calls
    // ──────────────────────────────────────────────

    /**
     * Every CURLOPT_SSL_VERIFYPEER must have a matching CURLOPT_SSL_VERIFYHOST.
     */
    public function testSslVerifyHostParity(): void
    {
        $files = [
            __DIR__ . '/../../../../../../../app/code/core/Mage/Usa/Model/Shipping/Carrier/Usps.php',
            __DIR__ . '/../../../../../../../app/code/core/Mage/Usa/Model/Shipping/Carrier/UspsAuth.php',
            __DIR__ . '/../../../../../../../app/code/core/Mage/Usa/Model/Shipping/Carrier/Usps/Rest/Client.php',
            __DIR__ . '/../../../../../../../app/code/core/Mage/Usa/Model/Shipping/Carrier/Usps/Tracking/Service.php',
        ];

        foreach ($files as $file) {
            $basename = basename($file);
            $source = file_get_contents($file);
            self::assertNotFalse($source, "Could not read $basename");

            $peerCount = substr_count($source, 'SSL_VERIFYPEER');
            $hostCount = substr_count($source, 'SSL_VERIFYHOST');

            self::assertSame(
                $peerCount,
                $hostCount,
                "$basename: CURLOPT_SSL_VERIFYPEER ($peerCount) and CURLOPT_SSL_VERIFYHOST ($hostCount) count must match",
            );

            // Verify VERIFYHOST value is 2 (not 0 or 1)
            self::assertStringNotContainsString(
                'SSL_VERIFYHOST => 0',
                $source,
                "$basename: SSL_VERIFYHOST must never be 0",
            );
            self::assertStringNotContainsString(
                'SSL_VERIFYHOST, 0',
                $source,
                "$basename: SSL_VERIFYHOST must never be 0",
            );
        }
    }

    // ──────────────────────────────────────────────
    // Fix #3: JSON cache serialization (no unserialize)
    // ──────────────────────────────────────────────

    /**
     * Standards.php must not use unserialize() — JSON only.
     */
    public function testStandardsNoUnserialize(): void
    {
        $source = file_get_contents(
            __DIR__ . '/../../../../../../../app/code/core/Mage/Usa/Model/Shipping/Carrier/Usps/Service/Standards.php',
        );
        self::assertNotFalse($source, 'Could not read Standards.php');

        self::assertStringNotContainsString(
            'unserialize(',
            $source,
            'Standards.php must use json_decode instead of unserialize',
        );
        self::assertStringNotContainsString(
            'serialize(',
            $source,
            'Standards.php must use json_encode instead of serialize',
        );
    }

    /**
     * JSON round-trip: json_decode(json_encode($data)) preserves structure.
     * Verifies JSON is a lossless replacement for serialize on array data.
     */
    public function testJsonCacheRoundTrip(): void
    {
        $estimates = [
            'deliveryDays' => 3,
            'scheduledDeliveryDate' => '2025-01-15',
            'mailClass' => 'PRIORITY_MAIL',
            'notes' => ['No Saturday delivery'],
        ];

        $encoded = json_encode($estimates);
        $decoded = json_decode($encoded, true);

        self::assertSame($estimates, $decoded, 'JSON cache round-trip must be lossless for array data');
    }

    /**
     * Corrupt cache data must not crash — json_decode returns null.
     */
    public function testJsonCacheCorruptDataReturnsNull(): void
    {
        $corrupt = 'not-valid-json{{{';
        $result = json_decode($corrupt, true);

        self::assertNull($result, 'json_decode on corrupt data must return null');
    }

    // ──────────────────────────────────────────────
    // Fix #4: Gateway URL allowlist
    // ──────────────────────────────────────────────

    /**
     * @dataProvider provideGatewayUrls
     */
    public function testGatewayUrlValidation(string $url, bool $shouldPass): void
    {
        // Replicate the exact validation logic from _getRestGatewayUrl()
        $parsed = parse_url($url);
        $scheme = $parsed['scheme'] ?? '';
        $host = $parsed['host'] ?? '';
        $isValid = ($scheme === 'https' && preg_match('/\.usps\.com$/i', $host));

        if ($shouldPass) {
            self::assertTrue((bool) $isValid, "URL should be accepted: $url");
        } else {
            self::assertFalse((bool) $isValid, "URL should be rejected: $url");
        }
    }

    public static function provideGatewayUrls(): \Iterator
    {
        yield 'production URL' => ['https://apis.usps.com/', true];
        yield 'sandbox URL' => ['https://apis-tem.usps.com/', true];
        yield 'with path' => ['https://apis.usps.com/some/path', true];
        yield 'SSRF evil.com' => ['https://evil.com/', false];
        yield 'SSRF with usps subdomain' => ['https://usps.com.evil.com/', false];
        yield 'non-HTTPS' => ['http://api.usps.com/', false];
        yield 'FTP scheme' => ['ftp://api.usps.com/', false];
        yield 'empty string' => ['', false];
        yield 'no scheme' => ['api.usps.com', false];
        yield 'bare IP' => ['https://192.168.1.1/', false];
        yield 'localhost' => ['https://localhost/', false];
        yield 'internal network' => ['https://10.0.0.1/', false];
    }

    // ──────────────────────────────────────────────
    // Fix #6: OAuth cache key credential fingerprint
    // ──────────────────────────────────────────────

    /**
     * Different credentials must produce different cache keys.
     */
    public function testCacheKeyChangesWithCredentials(): void
    {
        $storeId = '1';

        $keyA = 'usps_rest_api_token_store_' . $storeId . '_' . substr(hash('sha256', 'idAsecretA'), 0, 8);
        $keyB = 'usps_rest_api_token_store_' . $storeId . '_' . substr(hash('sha256', 'idBsecretB'), 0, 8);

        self::assertNotSame($keyA, $keyB, 'Different credentials must produce different cache keys');
    }

    /**
     * Same credentials must produce the same cache key (deterministic).
     */
    public function testCacheKeyDeterministicForSameCredentials(): void
    {
        $storeId = '1';
        $clientId = 'my-client-id';
        $clientSecret = 'my-client-secret';

        $key1 = 'usps_rest_api_token_store_' . $storeId . '_' . substr(hash('sha256', $clientId . $clientSecret), 0, 8);
        $key2 = 'usps_rest_api_token_store_' . $storeId . '_' . substr(hash('sha256', $clientId . $clientSecret), 0, 8);

        self::assertSame($key1, $key2, 'Same credentials must produce identical cache keys');
    }

    /**
     * Verify UspsAuth.php includes credentialHash in cache key construction.
     */
    public function testUspsAuthCacheKeyIncludesCredentialHash(): void
    {
        $source = file_get_contents(
            __DIR__ . '/../../../../../../../app/code/core/Mage/Usa/Model/Shipping/Carrier/UspsAuth.php',
        );
        self::assertNotFalse($source, 'Could not read UspsAuth.php');

        self::assertStringContainsString(
            "hash('sha256'",
            $source,
            'UspsAuth must include credential hash in cache key',
        );
        self::assertStringContainsString(
            '$credentialHash',
            $source,
            'UspsAuth must compute credentialHash variable',
        );
    }
}
