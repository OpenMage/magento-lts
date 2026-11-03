<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Dataflow\Model\Convert\Adapter\Http;

use Iterator;
use Mage;
use Mage_Dataflow_Model_Convert_Adapter_Http_Curl as Subject;
use Mage_Dataflow_Model_Convert_Exception;
use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Http_Adapter_Curl;

/**
 * The Dataflow curl adapter refuses loopback, link-local and other restricted destinations,
 * optionally private ones, and pins curl to the checked address (GHSA-989j-pjxc-9hgp)
 */
final class CurlDestinationTest extends OpenMageTest
{
    private CurlDestinationTestFixture $subject;

    private string $originalAllowedPrefixes;

    private string $originalAllowPrivateNetwork;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new CurlDestinationTestFixture();
        $this->subject->dns = [
            'feeds.example' => ['93.184.216.34'],
            'feeds6.example' => ['2606:2800:220:1:248:1893:25c8:1946'],
            'lan.example' => ['192.168.10.5'],
            'metadata.example' => ['169.254.169.254'],
            'mixed.example' => ['93.184.216.34', '127.0.0.1'],
        ];
        $this->originalAllowedPrefixes = (string) Mage::getStoreConfig(Subject::XML_PATH_ALLOWED_URL_PREFIXES);
        $this->originalAllowPrivateNetwork = (string) Mage::getStoreConfig(Subject::XML_PATH_ALLOW_PRIVATE_NETWORK);
        $this->setConfig(Subject::XML_PATH_ALLOWED_URL_PREFIXES, '');
        $this->setConfig(Subject::XML_PATH_ALLOW_PRIVATE_NETWORK, '1');
    }

    protected function tearDown(): void
    {
        $this->setConfig(Subject::XML_PATH_ALLOWED_URL_PREFIXES, $this->originalAllowedPrefixes);
        $this->setConfig(Subject::XML_PATH_ALLOW_PRIVATE_NETWORK, $this->originalAllowPrivateNetwork);
        parent::tearDown();
    }

    public function testPrivateNetworkIsAllowedByDefault(): void
    {
        self::assertSame('1', (string) Mage::getConfig()?->getNode('default/' . Subject::XML_PATH_ALLOW_PRIVATE_NETWORK));
    }

    /**
     * @dataProvider provideRestrictedDestinations
     */
    public function testRefusesRestrictedAddresses(string $uri): void
    {
        $this->expectException(Mage_Dataflow_Model_Convert_Exception::class);
        $this->expectExceptionMessage('network address that may not be fetched');

        $this->subject->restrictDestinationWrapper(new Varien_Http_Adapter_Curl(), $uri);
    }

    public static function provideRestrictedDestinations(): Iterator
    {
        yield 'loopback' => ['http://127.0.0.1:9200/_cat/indices'];
        yield 'cloud metadata' => ['http://169.254.169.254/latest/meta-data/'];
        yield 'unspecified' => ['http://0.0.0.0/'];
        yield 'IPv6 loopback' => ['http://[::1]/'];
        yield 'IPv4-mapped IPv6 loopback' => ['http://[::ffff:127.0.0.1]/'];
        yield 'host name resolving to metadata' => ['https://metadata.example/latest/meta-data/'];
        yield 'host name with one restricted address' => ['https://mixed.example/export.csv'];
    }

    public function testRefusesUnresolvableHost(): void
    {
        $this->expectException(Mage_Dataflow_Model_Convert_Exception::class);
        $this->expectExceptionMessage('cannot be resolved');

        $this->subject->restrictDestinationWrapper(new Varien_Http_Adapter_Curl(), 'https://unknown.example/export.csv');
    }

    public function testAllowsPrivateAddressWhenPrivateNetworkIsAllowed(): void
    {
        $http = new Varien_Http_Adapter_Curl();

        $url = $this->subject->restrictDestinationWrapper($http, 'http://lan.example:8080/export.csv');

        self::assertSame('http://lan.example:8080/export.csv', $url);
        self::assertSame(['lan.example:8080:192.168.10.5'], $this->getCurlOptions($http)[CURLOPT_RESOLVE]);
        self::assertSame(
            'http://192.168.10.5/export.csv',
            $this->subject->restrictDestinationWrapper(new Varien_Http_Adapter_Curl(), 'http://192.168.10.5/export.csv'),
        );
    }

    /**
     * @dataProvider providePrivateDestinations
     */
    public function testRefusesPrivateAddressWhenPrivateNetworkIsNotAllowed(string $uri): void
    {
        $this->setConfig(Subject::XML_PATH_ALLOW_PRIVATE_NETWORK, '0');

        $this->expectException(Mage_Dataflow_Model_Convert_Exception::class);
        $this->expectExceptionMessage('private network address');

        $this->subject->restrictDestinationWrapper(new Varien_Http_Adapter_Curl(), $uri);
    }

    public static function providePrivateDestinations(): Iterator
    {
        yield 'RFC 1918 literal' => ['http://10.1.2.3/export.csv'];
        yield 'host name on a LAN' => ['http://lan.example/export.csv'];
        yield 'unique local IPv6' => ['http://[fd12:3456:789a::1]/export.csv'];
    }

    public function testPinsTheCheckedAddressAndDoesNotFollowRedirects(): void
    {
        $http = new Varien_Http_Adapter_Curl();

        $url = $this->subject->restrictDestinationWrapper($http, 'https://Feeds.Example/exports/products.csv?format=csv#top');

        self::assertSame('https://feeds.example/exports/products.csv?format=csv', $url);
        $options = $this->getCurlOptions($http);
        self::assertSame(['feeds.example:443:93.184.216.34'], $options[CURLOPT_RESOLVE]);
        self::assertFalse($options[CURLOPT_FOLLOWLOCATION]);
    }

    public function testPinsIpv6AddressInBrackets(): void
    {
        $http = new Varien_Http_Adapter_Curl();

        $this->subject->restrictDestinationWrapper($http, 'http://feeds6.example/products.csv');

        self::assertSame(
            ['feeds6.example:80:[2606:2800:220:1:248:1893:25c8:1946]'],
            $this->getCurlOptions($http)[CURLOPT_RESOLVE],
        );
    }

    public function testKeepsCredentialsAndDoesNotPinAnAddressLiteral(): void
    {
        $http = new Varien_Http_Adapter_Curl();

        $url = $this->subject->restrictDestinationWrapper($http, 'http://user:secret@93.184.216.34/export.csv');

        self::assertSame('http://user:secret@93.184.216.34/export.csv', $url);
        self::assertArrayNotHasKey(CURLOPT_RESOLVE, $this->getCurlOptions($http));
    }

    public function testAllowedUrlPrefixIsExemptFromTheAddressCheck(): void
    {
        $this->setConfig(Subject::XML_PATH_ALLOWED_URL_PREFIXES, 'http://127.0.0.1:8080/exports/');
        $http = new Varien_Http_Adapter_Curl();

        $url = $this->subject->restrictDestinationWrapper($http, 'http://127.0.0.1:8080/exports/products.csv');

        self::assertSame('http://127.0.0.1:8080/exports/products.csv', $url);
        self::assertArrayNotHasKey(CURLOPT_RESOLVE, $this->getCurlOptions($http));
    }

    private function setConfig(string $path, string $value): void
    {
        Mage::app()->getStore()->setConfig($path, $value);
    }

    /**
     * @return array<int, mixed>
     */
    private function getCurlOptions(Varien_Http_Adapter_Curl $http): array
    {
        return (fn(): array => $this->_options)->call($http);
    }
}
