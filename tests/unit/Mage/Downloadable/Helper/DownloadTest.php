<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Downloadable\Helper;

use Iterator;
use Mage;
use Mage_Core_Exception;
use Mage_Downloadable_Helper_Download as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use Override;

final class DownloadTest extends OpenMageTest
{
    private Subject $subject;

    private string $originalAllowedPrefixes;

    private string $originalAllowPrivateNetwork;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new class extends Subject {
            /** @var array<string, string[]> */
            public array $dns = [];

            /** @var list<array{0: string, 1: string, 2: string}> */
            public array $reports = [];

            public function setUrl(string $url): void
            {
                $this->_resourceFile = $url;
                $this->_linkType = self::LINK_TYPE_URL;
                $this->_handle = null;
            }

            public function getHandleWrapper(): mixed
            {
                return $this->_getHandle();
            }

            #[Override]
            protected function _resolveHost($host)
            {
                $host = trim($host, '[]');
                if (filter_var($host, FILTER_VALIDATE_IP)) {
                    return [$host];
                }

                return $this->dns[$host] ?? [];
            }

            #[Override]
            protected function _reportBlockedUrl(array $urlProp, string $address, string $addressClass): void
            {
                $this->reports[] = [$this->_getUrlOrigin($urlProp), $address, $addressClass];
            }

            public function validateUrlWrapper($url): array
            {
                return $this->_validateUrl($url);
            }

            public function getAllowedAddressWrapper($host, $checkAddress = true): string
            {
                return $this->_getAllowedAddress($host, $checkAddress);
            }

            public function isPublicAddressWrapper($address): bool
            {
                return $this->_isPublicAddress($address);
            }

            public function isAllowedByPrefixWrapper($url): bool
            {
                return $this->_isAllowedByPrefix($url);
            }

            public function getAllowedUrlPrefixesWrapper(): array
            {
                return $this->_getAllowedUrlPrefixes();
            }
        };
        $this->originalAllowedPrefixes = (string) Mage::getStoreConfig(Subject::XML_PATH_LINK_URL_ALLOWED_PREFIXES);
        $this->originalAllowPrivateNetwork = (string) Mage::getStoreConfig(Subject::XML_PATH_LINK_URL_ALLOW_PRIVATE_NETWORK);
        $this->setAllowedPrefixes('');
        $this->setAllowPrivateNetwork(false);
    }

    protected function tearDown(): void
    {
        $this->setAllowedPrefixes($this->originalAllowedPrefixes);
        Mage::app()->getStore()->setConfig(Subject::XML_PATH_LINK_URL_ALLOW_PRIVATE_NETWORK, $this->originalAllowPrivateNetwork);
        parent::tearDown();
    }

    /**
     * @dataProvider provideInvalidSchemes
     */
    public function testValidateUrlRejectsNonHttpSchemes(string $url): void
    {
        $this->expectException(Mage_Core_Exception::class);
        $this->expectExceptionMessage('Invalid download URL scheme.');

        $this->subject->validateUrlWrapper($url);
    }

    public function testValidateUrlAcceptsHttpAndHttps(): void
    {
        self::assertSame('http', $this->subject->validateUrlWrapper('http://example.com/file.zip')['scheme']);
        self::assertSame('https', $this->subject->validateUrlWrapper('https://example.com/file.zip')['scheme']);
    }

    /**
     * @dataProvider provideNonPublicHosts
     */
    public function testGetAllowedAddressRejectsNonPublicHosts(string $host): void
    {
        $this->expectException(Mage_Core_Exception::class);
        $this->expectExceptionMessage('Invalid download URL host.');

        $this->subject->getAllowedAddressWrapper($host);
    }

    public function testGetAllowedAddressReturnsPublicAddress(): void
    {
        self::assertSame('93.184.216.34', $this->subject->getAllowedAddressWrapper('93.184.216.34'));
    }

    public function testGetAllowedAddressSkipsAddressCheckWhenNotRequested(): void
    {
        self::assertSame('127.0.0.1', $this->subject->getAllowedAddressWrapper('127.0.0.1', false));
    }

    /**
     * @dataProvider provideNonPublicHosts
     */
    public function testIsPublicAddressRejectsReservedRanges(string $address): void
    {
        self::assertFalse($this->subject->isPublicAddressWrapper(trim($address, '[]')));
    }

    public function testGetAllowedUrlPrefixesSplitsConfiguredValue(): void
    {
        $this->setAllowedPrefixes("https://allowed.example/files/\nhttps://cdn.example/, http://intranet.example/");

        self::assertSame(
            ['https://allowed.example/files/', 'https://cdn.example/', 'http://intranet.example/'],
            $this->subject->getAllowedUrlPrefixesWrapper(),
        );
    }

    public function testIsAllowedByPrefix(): void
    {
        $this->setAllowedPrefixes("https://allowed.example/files/\nhttps://cdn.example/");

        self::assertTrue($this->subject->isAllowedByPrefixWrapper('https://allowed.example/files/a.zip'));
        self::assertFalse($this->subject->isAllowedByPrefixWrapper('https://blocked.example/files/a.zip'));
    }

    public function testIsAllowedByPrefixIsFalseWhenNothingIsConfigured(): void
    {
        self::assertFalse($this->subject->isAllowedByPrefixWrapper('https://example.com/a.zip'));
    }

    /**
     * @dataProvider provideAddressClasses
     */
    public function testGetAddressClass(string $address, string $expected): void
    {
        self::assertSame($expected, $this->subject->getAddressClass($address));
    }

    public function testPrivateAddressIsAllowedOnlyWhenPrivateNetworkIsAllowed(): void
    {
        $this->setAllowPrivateNetwork(true);
        self::assertSame('10.0.0.5', $this->subject->getAllowedAddressWrapper('10.0.0.5'));

        $this->setAllowPrivateNetwork(false);
        $this->expectException(Mage_Core_Exception::class);
        $this->subject->getAllowedAddressWrapper('10.0.0.5');
    }

    /**
     * @dataProvider provideRestrictedHosts
     */
    public function testRestrictedAddressIsRefusedEvenWhenPrivateNetworkIsAllowed(string $host): void
    {
        $this->setAllowPrivateNetwork(true);

        $this->expectException(Mage_Core_Exception::class);
        $this->expectExceptionMessage('Invalid download URL host.');

        $this->subject->getAllowedAddressWrapper($host);
    }

    public function testHostnameIsRefusedIfAnyResolvedAddressIsRestricted(): void
    {
        $this->subject->dns['mixed.example'] = ['93.184.216.34', '169.254.169.254'];

        $this->expectException(Mage_Core_Exception::class);
        $this->subject->getAllowedAddressWrapper('mixed.example');
    }

    /**
     * @dataProvider provideUrlsNotCoveredByPrefix
     */
    public function testIsAllowedByPrefixRejectsLookalikes(string $url): void
    {
        $this->setAllowedPrefixes('http://files.example.lan');

        self::assertFalse($this->subject->isAllowedByPrefixWrapper($url));
    }

    public function testIsAllowedByPrefixMatchesHostOnlyPrefixAndPath(): void
    {
        $this->setAllowedPrefixes("http://files.example.lan\nhttps://cdn.example/downloads/");

        self::assertTrue($this->subject->isAllowedByPrefixWrapper('http://files.example.lan'));
        self::assertTrue($this->subject->isAllowedByPrefixWrapper('http://FILES.example.lan:80/a.zip'));
        self::assertTrue($this->subject->isAllowedByPrefixWrapper('https://cdn.example/downloads/a.zip?sig=1'));
        self::assertFalse($this->subject->isAllowedByPrefixWrapper('https://cdn.example/other/a.zip'));
        self::assertFalse($this->subject->isAllowedByPrefixWrapper('https://cdn.example/downloads/../other/a.zip'));
        self::assertFalse($this->subject->isAllowedByPrefixWrapper('https://cdn.example/downloads/%2e%2e/other/a.zip'));
    }

    public function testTrustedPrefixDoesNotRestrictOtherPublicHosts(): void
    {
        $this->setAllowedPrefixes('http://files.example.lan/');
        $this->subject->dns['public.example'] = ['93.184.216.34'];

        $result = $this->subject->inspectLinkUrl('https://public.example/a.zip');

        self::assertTrue($result['allowed']);
        self::assertFalse($result['trusted']);
    }

    public function testTrustedPrefixExemptsPrivateHost(): void
    {
        $this->setAllowedPrefixes('http://files.example.lan/');
        $this->subject->dns['files.example.lan'] = ['10.1.2.3'];

        $result = $this->subject->inspectLinkUrl('http://files.example.lan/a.zip');

        self::assertTrue($result['allowed']);
        self::assertTrue($result['trusted']);
        self::assertSame(Subject::ADDRESS_PRIVATE, $result['address_class']);
    }

    public function testInspectLinkUrlReportsRefusal(): void
    {
        $this->subject->dns['rebind.example'] = ['127.0.0.1'];

        $result = $this->subject->inspectLinkUrl('http://rebind.example:8080/x');

        self::assertFalse($result['allowed']);
        self::assertSame('http://rebind.example:8080', $result['origin']);
        self::assertSame('127.0.0.1', $result['address']);
        self::assertSame(Subject::ADDRESS_RESTRICTED, $result['address_class']);
    }

    public function testInspectLinkUrlReportsInvalidUrl(): void
    {
        $result = $this->subject->inspectLinkUrl('file:///etc/passwd');

        self::assertFalse($result['allowed']);
        self::assertSame('Invalid download URL scheme.', $result['reason']);
    }

    public function testGetHandleRefusesRestrictedAddressAndReportsIt(): void
    {
        $this->subject->dns['metadata.example'] = ['169.254.169.254'];
        $this->subject->setUrl('http://metadata.example/latest/meta-data/');

        try {
            $this->subject->getHandleWrapper();
            self::fail('Expected the download to be refused');
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertSame('Invalid download URL host.', $mageCoreException->getMessage());
        }

        self::assertSame([['http://metadata.example', '169.254.169.254', Subject::ADDRESS_RESTRICTED]], $this->subject->reports);
    }

    public function testGetHandleDoesNotReportUnresolvableHost(): void
    {
        $this->subject->setUrl('http://does-not-resolve.example/a.zip');

        try {
            $this->subject->getHandleWrapper();
            self::fail('Expected the download to fail');
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertSame('Cannot resolve the download URL host.', $mageCoreException->getMessage());
        }

        self::assertSame([], $this->subject->reports);
    }

    public static function provideAddressClasses(): Iterator
    {
        yield 'public ipv4' => ['93.184.216.34', Subject::ADDRESS_PUBLIC];
        yield 'public ipv6' => ['2606:2800:220:1:248:1893:25c8:1946', Subject::ADDRESS_PUBLIC];
        yield 'rfc1918 10/8' => ['10.0.0.5', Subject::ADDRESS_PRIVATE];
        yield 'rfc1918 172.16/12' => ['172.31.255.255', Subject::ADDRESS_PRIVATE];
        yield 'outside 172.16/12' => ['172.32.0.1', Subject::ADDRESS_PUBLIC];
        yield 'rfc1918 192.168/16' => ['192.168.1.10', Subject::ADDRESS_PRIVATE];
        yield 'carrier-grade nat' => ['100.64.0.1', Subject::ADDRESS_PRIVATE];
        yield 'unique local ipv6' => ['fd12:3456::1', Subject::ADDRESS_PRIVATE];
        yield 'loopback' => ['127.0.0.1', Subject::ADDRESS_RESTRICTED];
        yield 'loopback high' => ['127.255.255.254', Subject::ADDRESS_RESTRICTED];
        yield 'unspecified' => ['0.0.0.0', Subject::ADDRESS_RESTRICTED];
        yield 'this network' => ['0.1.2.3', Subject::ADDRESS_RESTRICTED];
        yield 'link local, cloud metadata' => ['169.254.169.254', Subject::ADDRESS_RESTRICTED];
        yield 'alibaba cloud metadata' => ['100.100.100.200', Subject::ADDRESS_RESTRICTED];
        yield 'ietf protocol assignments' => ['192.0.0.192', Subject::ADDRESS_RESTRICTED];
        yield 'benchmarking' => ['198.18.0.1', Subject::ADDRESS_RESTRICTED];
        yield 'multicast' => ['224.0.0.1', Subject::ADDRESS_RESTRICTED];
        yield 'broadcast' => ['255.255.255.255', Subject::ADDRESS_RESTRICTED];
        yield 'ipv6 loopback' => ['::1', Subject::ADDRESS_RESTRICTED];
        yield 'ipv6 unspecified' => ['::', Subject::ADDRESS_RESTRICTED];
        yield 'ipv6 link local' => ['fe80::1', Subject::ADDRESS_RESTRICTED];
        yield 'ipv6 multicast' => ['ff02::1', Subject::ADDRESS_RESTRICTED];
        yield 'aws ipv6 metadata' => ['fd00:ec2::254', Subject::ADDRESS_RESTRICTED];
        yield 'teredo' => ['2001:0:4136:e378:8000:63bf:3fff:fdd2', Subject::ADDRESS_RESTRICTED];
        yield 'ipv4-mapped loopback' => ['::ffff:127.0.0.1', Subject::ADDRESS_RESTRICTED];
        yield 'ipv4-mapped metadata' => ['::ffff:a9fe:a9fe', Subject::ADDRESS_RESTRICTED];
        yield 'ipv4-mapped private' => ['::ffff:10.0.0.1', Subject::ADDRESS_PRIVATE];
        yield 'ipv4-mapped public' => ['::ffff:93.184.216.34', Subject::ADDRESS_PUBLIC];
        yield 'ipv4-compatible loopback' => ['::127.0.0.1', Subject::ADDRESS_RESTRICTED];
        yield 'ipv4-compatible low' => ['::5', Subject::ADDRESS_RESTRICTED];
        yield 'nat64 metadata' => ['64:ff9b::a9fe:a9fe', Subject::ADDRESS_RESTRICTED];
        yield 'nat64 public' => ['64:ff9b::5db8:d822', Subject::ADDRESS_PUBLIC];
        yield '6to4 metadata' => ['2002:a9fe:a9fe::1', Subject::ADDRESS_RESTRICTED];
        yield '6to4 private' => ['2002:0a00:0001::1', Subject::ADDRESS_PRIVATE];
        yield 'bracketed ipv6' => ['[::1]', Subject::ADDRESS_RESTRICTED];
        yield 'not an address' => ['localhost', Subject::ADDRESS_RESTRICTED];
        yield 'empty' => ['', Subject::ADDRESS_RESTRICTED];
    }

    public static function provideRestrictedHosts(): Iterator
    {
        yield 'loopback' => ['127.0.0.1'];
        yield 'link local, cloud metadata' => ['169.254.169.254'];
        yield 'alibaba cloud metadata' => ['100.100.100.200'];
        yield 'aws ipv6 metadata' => ['[fd00:ec2::254]'];
        yield 'ipv4-mapped metadata' => ['[::ffff:169.254.169.254]'];
    }

    public static function provideUrlsNotCoveredByPrefix(): Iterator
    {
        yield 'longer host' => ['http://files.example.lan.attacker.test/a.zip'];
        yield 'userinfo' => ['http://files.example.lan@169.254.169.254/latest/meta-data/'];
        yield 'userinfo with password' => ['http://files.example.lan:x@127.0.0.1/'];
        yield 'other port' => ['http://files.example.lan:8080/a.zip'];
        yield 'other scheme' => ['https://files.example.lan/a.zip'];
    }

    public static function provideInvalidSchemes(): Iterator
    {
        yield 'file' => ['file:///etc/passwd'];
        yield 'ftp' => ['ftp://example.com/file.zip'];
        yield 'gopher' => ['gopher://127.0.0.1:11211/_stats'];
        yield 'php' => ['php://filter/read=convert.base64-encode/resource=app/etc/local.xml'];
        yield 'data' => ['data://text/plain;base64,SGVsbG8='];
    }

    public static function provideNonPublicHosts(): Iterator
    {
        yield 'loopback' => ['127.0.0.1'];
        yield 'loopback ipv6' => ['[::1]'];
        yield 'unspecified' => ['0.0.0.0'];
        yield 'link local, cloud metadata' => ['169.254.169.254'];
        yield 'rfc1918 class a' => ['10.0.0.5'];
        yield 'rfc1918 class b' => ['172.16.0.5'];
        yield 'rfc1918 class c' => ['192.168.1.10'];
        yield 'unique local ipv6' => ['[fd00::1]'];
    }

    private function setAllowedPrefixes(string $value): void
    {
        Mage::app()->getStore()->setConfig(Subject::XML_PATH_LINK_URL_ALLOWED_PREFIXES, $value);
    }

    private function setAllowPrivateNetwork(bool $value): void
    {
        Mage::app()->getStore()->setConfig(Subject::XML_PATH_LINK_URL_ALLOW_PRIVATE_NETWORK, $value ? '1' : '0');
    }
}
