<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Downloadable\Helper;

use Mage;
use Mage_Core_Exception;
use Mage_Downloadable_Helper_Download as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use Override;

final class DownloadTest extends OpenMageTest
{
    private Subject $subject;

    private string $originalAllowedPrefixes;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new class extends Subject {
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
        $this->setAllowedPrefixes('');
    }

    #[Override]
    protected function tearDown(): void
    {
        $this->setAllowedPrefixes($this->originalAllowedPrefixes);
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

    public static function provideInvalidSchemes(): array
    {
        return [
            'file' => ['file:///etc/passwd'],
            'ftp' => ['ftp://example.com/file.zip'],
            'gopher' => ['gopher://127.0.0.1:11211/_stats'],
            'php' => ['php://filter/read=convert.base64-encode/resource=app/etc/local.xml'],
            'data' => ['data://text/plain;base64,SGVsbG8='],
        ];
    }

    public static function provideNonPublicHosts(): array
    {
        return [
            'loopback' => ['127.0.0.1'],
            'loopback ipv6' => ['[::1]'],
            'unspecified' => ['0.0.0.0'],
            'link local, cloud metadata' => ['169.254.169.254'],
            'rfc1918 class a' => ['10.0.0.5'],
            'rfc1918 class b' => ['172.16.0.5'],
            'rfc1918 class c' => ['192.168.1.10'],
            'unique local ipv6' => ['[fd00::1]'],
        ];
    }

    private function setAllowedPrefixes(string $value): void
    {
        Mage::app()->getStore()->setConfig(Subject::XML_PATH_LINK_URL_ALLOWED_PREFIXES, $value);
    }
}
