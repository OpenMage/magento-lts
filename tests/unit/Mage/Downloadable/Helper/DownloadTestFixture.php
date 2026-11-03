<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Downloadable\Helper;

use Mage_Downloadable_Helper_Download;
use Override;

/**
 * Resolves host names from $dns instead of DNS, records refusal reports and exposes the URL checks
 */
final class DownloadTestFixture extends Mage_Downloadable_Helper_Download
{
    /**
     * @var array<string, list<string>>
     */
    public array $dns = [];

    /**
     * @var list<array{0: string, 1: string, 2: string}>
     */
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
        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return [$host];
        }

        return $this->dns[$host] ?? [];
    }

    #[Override]
    protected function _reportBlockedUrl(array $urlProp, string $address, string $addressClass): void
    {
        $this->reports[] = [$this->_getUrlOrigin($urlProp), $address, $addressClass];
    }

    /**
     * @return array{scheme: string, host: string, port?: int, user?: string, pass?: string, path?: string, query?: string, fragment?: string}
     */
    public function validateUrlWrapper(string $url): array
    {
        return $this->_validateUrl($url);
    }

    public function getAllowedAddressWrapper(string $host, bool $checkAddress = true): string
    {
        return $this->_getAllowedAddress($host, $checkAddress);
    }

    public function isPublicAddressWrapper(string $address): bool
    {
        return $this->_isPublicAddress($address);
    }

    public function isAllowedByPrefixWrapper(string $url): bool
    {
        return $this->_isAllowedByPrefix($url);
    }

    /**
     * @return list<string>
     */
    public function getAllowedUrlPrefixesWrapper(): array
    {
        return $this->_getAllowedUrlPrefixes();
    }
}
