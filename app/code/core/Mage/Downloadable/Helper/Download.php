<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

use Monolog\Level;

/**
 * Downloadable Products Download Helper
 *
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Helper_Download extends Mage_Core_Helper_Abstract
{
    public const LINK_TYPE_URL         = 'url';

    public const LINK_TYPE_FILE        = 'file';

    public const XML_PATH_CONTENT_DISPOSITION  = 'catalog/downloadable/content_disposition';

    public const XML_PATH_LINK_URL_ALLOWED_PREFIXES = 'catalog/downloadable/link_url_allowed_prefixes';

    public const XML_PATH_LINK_URL_ALLOW_PRIVATE_NETWORK = 'catalog/downloadable/link_url_allow_private_network';

    /**
     * Address classes returned by getAddressClass()
     */
    public const ADDRESS_PUBLIC     = 'public';

    public const ADDRESS_PRIVATE    = 'private';

    public const ADDRESS_RESTRICTED = 'restricted';

    /**
     * Destinations that are never fetched unless a trusted URL prefix covers them: loopback,
     * link-local (cloud instance metadata), unspecified, multicast, documentation, benchmarking
     * and other special-purpose ranges, plus known metadata addresses that sit inside otherwise
     * private ranges (Alibaba Cloud 100.100.100.200, AWS IPv6 fd00:ec2::254).
     *
     * Kept as an explicit table rather than FILTER_FLAG_NO_RES_RANGE, whose coverage differs
     * between PHP versions and omits several of these ranges.
     */
    protected const RESTRICTED_RANGES = [
        '0.0.0.0/8',
        '127.0.0.0/8',
        '169.254.0.0/16',
        '192.0.0.0/24',
        '192.0.2.0/24',
        '198.18.0.0/15',
        '198.51.100.0/24',
        '203.0.113.0/24',
        '224.0.0.0/4',
        '240.0.0.0/4',
        '100.100.100.200/32',
        '::/128',
        '::1/128',
        '64:ff9b:1::/48',
        '100::/64',
        '2001::/23',
        '2001:db8::/32',
        'fd00:ec2::254/128',
        'fe80::/10',
        'fec0::/10',
        'ff00::/8',
    ];

    /**
     * Private-use destinations, fetched only when the "allow private network" setting is on
     */
    protected const PRIVATE_RANGES = [
        '10.0.0.0/8',
        '100.64.0.0/10',
        '172.16.0.0/12',
        '192.168.0.0/16',
        'fc00::/7',
    ];

    protected $_moduleName = 'Mage_Downloadable';

    /**
     * Type of link
     *
     * @var string
     */
    protected $_linkType        = self::LINK_TYPE_FILE;

    /**
     * Resource file
     *
     * @var string
     */
    protected $_resourceFile    = null;

    /**
     * Resource open handle
     *
     * @var null|resource|Varien_Io_File
     */
    protected $_handle          = null;

    /**
     * Remote server headers
     *
     * @var array
     */
    protected $_urlHeaders      = [];

    /**
     * MIME Content-type for a file
     *
     * @var string
     */
    protected $_contentType     = 'application/octet-stream';

    /**
     * File name
     *
     * @var string
     */
    protected $_fileName        = 'download';

    /**
     * Address and address class that made the last URL check fail, if it failed on the address
     *
     * @var null|array{0: string, 1: string}
     */
    protected $_blockedAddress  = null;

    /**
     * Retrieve Resource file handle (socket, file pointer etc)
     *
     * @return resource|Varien_Io_File
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    protected function _getHandle()
    {
        if (!$this->_resourceFile) {
            Mage::throwException(Mage::helper('downloadable')->__('Please set resource file and link type.'));
        }

        if (is_null($this->_handle)) {
            if ($this->_linkType == self::LINK_TYPE_URL) {
                /**
                 * Validate URL
                 */
                $urlProp = $this->_validateUrl($this->_resourceFile);
                $isAllowedByPrefix = $this->_isAllowedByPrefix($this->_resourceFile);

                switch ($urlProp['scheme']) {
                    case 'https':
                        $transport = 'ssl://';
                        $port = 443;
                        break;
                    case 'http':
                    default:
                        $transport = 'tcp://';
                        $port = 80;
                }

                if (isset($urlProp['port'])) {
                    $port = $urlProp['port'];
                }

                $path = $urlProp['path'] ?? '/';
                $query = '';
                if (isset($urlProp['query'])) {
                    $query = '?' . $urlProp['query'];
                }

                /**
                 * Connect to the validated address instead of the hostname so that the destination
                 * cannot be swapped between validation and connection (DNS rebinding)
                 */
                $this->_blockedAddress = null;
                try {
                    $address = $this->_getAllowedAddress($urlProp['host'], !$isAllowedByPrefix);
                } catch (Mage_Core_Exception $exception) {
                    if ($this->_blockedAddress !== null) {
                        $this->_reportBlockedUrl($urlProp, ...$this->_blockedAddress);
                    }

                    throw $exception;
                }

                $remote = $transport . (str_contains($address, ':') ? '[' . $address . ']' : $address) . ':' . $port;

                $context = stream_context_create();
                if ($transport === 'ssl://') {
                    stream_context_set_option($context, 'ssl', 'peer_name', trim($urlProp['host'], '[]'));
                }

                $this->_handle = stream_socket_client(
                    $remote,
                    $errno,
                    $errstr,
                    (float) ini_get('default_socket_timeout'),
                    STREAM_CLIENT_CONNECT,
                    $context,
                );

                if ($this->_handle === false) {
                    Mage::throwException(Mage::helper('downloadable')->__('Cannot connect to remote host, error: %s.', $errstr));
                }

                $headers = 'GET ' . $path . $query . ' HTTP/1.0' . "\r\n"
                    . 'Host: ' . $urlProp['host'] . "\r\n"
                    . 'User-Agent: Magento ver/' . Mage::getVersion() . "\r\n"
                    . 'Connection: close' . "\r\n"
                    . "\r\n";
                fwrite($this->_handle, $headers);

                while (!feof($this->_handle)) {
                    $str = fgets($this->_handle, 1024);
                    if ($str == "\r\n") {
                        break;
                    }

                    $match = [];
                    if (preg_match('#^([^:]+): (.*)\s+$#', $str, $match)) {
                        $key = strtolower($match[1]);
                        if ($key === 'set-cookie') {
                            continue;
                        }

                        $this->_urlHeaders[$key] = trim($match[2]);
                    } elseif (preg_match('#^HTTP/[0-9\.]+ (\d+) (.*)\s$#', $str, $match)) {
                        $this->_urlHeaders['code'] = $match[1];
                        $this->_urlHeaders['code-string'] = trim($match[2]);
                    }
                }

                if (!isset($this->_urlHeaders['code']) || $this->_urlHeaders['code'] != 200) {
                    Mage::throwException(Mage::helper('downloadable')->__('An error occurred while getting the requested content. Please contact the store owner.'));
                }
            } elseif ($this->_linkType == self::LINK_TYPE_FILE) {
                $this->_handle = new Varien_Io_File();
                if (!is_file($this->_resourceFile)) {
                    Mage::helper('core/file_storage_database')->saveFileToFilesystem($this->_resourceFile);
                }

                $this->_handle->open(['path' => Mage::getBaseDir('var')]);
                if (!$this->_handle->fileExists($this->_resourceFile, true)) {
                    Mage::throwException(Mage::helper('downloadable')->__('The file does not exist.'));
                }

                $this->_handle->streamOpen($this->_resourceFile, 'r');
            } else {
                Mage::throwException(Mage::helper('downloadable')->__('Invalid download link type.'));
            }
        }

        return $this->_handle;
    }

    /**
     * Validate the download URL and return its parsed components
     *
     * Only the http and https schemes are accepted, every other stream wrapper
     * (file, ftp, gopher, php, data, ...) is rejected before anything is fetched.
     *
     * @param  string              $url
     * @return array
     * @throws Mage_Core_Exception
     */
    protected function _validateUrl($url)
    {
        /** @var Mage_Core_Helper_Validate $validator */
        $validator = Mage::helper('core/validate');
        if ($validator->validateUrl(value: $url, protocols: ['http', 'https'])->count() > 0) {
            Mage::throwException(Mage::helper('downloadable')->__('Invalid download URL scheme.'));
        }

        $urlProp = parse_url($url);
        if (!isset($urlProp['scheme'])
            || strtolower($urlProp['scheme']) !== 'http' && strtolower($urlProp['scheme']) !== 'https'
        ) {
            Mage::throwException(Mage::helper('downloadable')->__('Invalid download URL scheme.'));
        }

        if (!isset($urlProp['host'])) {
            Mage::throwException(Mage::helper('downloadable')->__('Invalid download URL host.'));
        }

        $urlProp['scheme'] = strtolower($urlProp['scheme']);

        return $urlProp;
    }

    /**
     * Resolve the host and return the address the download may be fetched from
     *
     * Unless the URL is covered by a trusted prefix, every address the host resolves to must
     * be allowed by getAddressClass(): public addresses always, private-use addresses only
     * when the "allow private network" setting is on, restricted addresses (loopback,
     * link-local, cloud instance metadata, ...) never.
     *
     * @param  string              $host
     * @param  bool                $checkAddress
     * @return string
     * @throws Mage_Core_Exception
     */
    protected function _getAllowedAddress($host, $checkAddress = true)
    {
        $addresses = $this->_resolveHost($host);
        if (!$addresses) {
            Mage::throwException(Mage::helper('downloadable')->__('Cannot resolve the download URL host.'));
        }

        if ($checkAddress) {
            foreach ($addresses as $address) {
                $class = $this->getAddressClass($address);
                if (!$this->_isAllowedAddressClass($class)) {
                    $this->_blockedAddress = [$address, $class];
                    Mage::throwException(Mage::helper('downloadable')->__('Invalid download URL host.'));
                }
            }
        }

        return reset($addresses);
    }

    /**
     * Retrieve all IPv4 and IPv6 addresses the given host resolves to
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     * @param  string $host
     * @return array
     */
    protected function _resolveHost($host)
    {
        $host = trim($host, '[]');
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return [$host];
        }

        $addresses = gethostbynamel($host) ?: [];
        foreach (@dns_get_record($host, DNS_AAAA) ?: [] as $record) {
            if (!empty($record['ipv6'])) {
                $addresses[] = $record['ipv6'];
            }
        }

        return $addresses;
    }

    /**
     * Check whether the given address is publicly routable
     *
     * @param  string $address
     * @return bool
     */
    protected function _isPublicAddress($address)
    {
        return $this->getAddressClass((string) $address) === self::ADDRESS_PUBLIC;
    }

    /**
     * Classify an IP address as public, private-use or restricted
     *
     * IPv6 addresses that embed an IPv4 address (IPv4-mapped, IPv4-compatible, NAT64 and
     * 6to4) are classified by the embedded IPv4 address. Anything that is not a valid IP
     * address is restricted.
     */
    public function getAddressClass(string $address): string
    {
        $packed = @inet_pton(trim($address, '[]'));
        if ($packed === false) {
            return self::ADDRESS_RESTRICTED;
        }

        if (strlen($packed) === 16) {
            $embedded = $this->_getEmbeddedIpv4($packed);
            if ($embedded !== null) {
                return $this->getAddressClass((string) inet_ntop($embedded));
            }
        }

        foreach (static::RESTRICTED_RANGES as $range) {
            if ($this->_isAddressInRange($packed, $range)) {
                return self::ADDRESS_RESTRICTED;
            }
        }

        foreach (static::PRIVATE_RANGES as $range) {
            if ($this->_isAddressInRange($packed, $range)) {
                return self::ADDRESS_PRIVATE;
            }
        }

        return self::ADDRESS_PUBLIC;
    }

    /**
     * Whether private-use destinations (RFC 1918, carrier-grade NAT, unique local IPv6) may be fetched
     */
    public function isPrivateNetworkAllowed(): bool
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_LINK_URL_ALLOW_PRIVATE_NETWORK);
    }

    /**
     * Inspect a URL type link without fetching it
     *
     * Applies exactly the checks used when the link is downloaded, so that the result can be
     * used to tell a merchant which of their existing links would be refused.
     *
     * @return array{origin: string, allowed: bool, trusted: bool, address: null|string, address_class: null|string, reason: null|string}
     */
    public function inspectLinkUrl(string $url): array
    {
        $result = [
            'origin'        => $url,
            'allowed'       => false,
            'trusted'       => false,
            'address'       => null,
            'address_class' => null,
            'reason'        => null,
        ];

        try {
            $urlProp = $this->_validateUrl($url);
            $result['origin'] = $this->_getUrlOrigin($urlProp);
            $result['trusted'] = $this->_isAllowedByPrefix($url);

            $addresses = $this->_resolveHost($urlProp['host']);
            if (!$addresses) {
                $result['reason'] = Mage::helper('downloadable')->__('Cannot resolve the download URL host.');
                return $result;
            }

            foreach ($addresses as $address) {
                $class = $this->getAddressClass($address);
                if ($result['address_class'] === null || $class !== self::ADDRESS_PUBLIC) {
                    $result['address'] = $address;
                    $result['address_class'] = $class;
                }

                if (!$result['trusted'] && !$this->_isAllowedAddressClass($class)) {
                    $result['reason'] = Mage::helper('downloadable')->__('Invalid download URL host.');
                    return $result;
                }
            }

            $result['allowed'] = true;
        } catch (Mage_Core_Exception $mageCoreException) {
            $result['reason'] = $mageCoreException->getMessage();
        }

        return $result;
    }

    /**
     * Whether a trusted URL prefix covers the URL, which exempts it from the address check
     */
    public function isTrustedLinkUrl(string $url): bool
    {
        return $this->_isAllowedByPrefix($url);
    }

    /**
     * @param string $class one of the ADDRESS_* constants
     */
    protected function _isAllowedAddressClass(string $class): bool
    {
        return $class === self::ADDRESS_PUBLIC
            || ($class === self::ADDRESS_PRIVATE && $this->isPrivateNetworkAllowed());
    }

    /**
     * Return the IPv4 address embedded in an IPv6 address, if any
     *
     * @param  string      $packed 16 byte in_addr6
     * @return null|string 4 byte in_addr
     */
    protected function _getEmbeddedIpv4(string $packed): ?string
    {
        // ::ffff:0:0/96 IPv4-mapped
        if (str_starts_with($packed, str_repeat("\0", 10) . "\xff\xff")) {
            return substr($packed, 12, 4);
        }

        // ::/96 IPv4-compatible (deprecated); :: and ::1 are left to the range table
        if (str_starts_with($packed, str_repeat("\0", 12))
            && !in_array(substr($packed, 12), ["\0\0\0\0", "\0\0\0\1"], true)
        ) {
            return substr($packed, 12, 4);
        }

        // 64:ff9b::/96 NAT64 well-known prefix
        if (str_starts_with($packed, "\x00\x64\xff\x9b" . str_repeat("\0", 8))) {
            return substr($packed, 12, 4);
        }

        // 2002::/16 6to4
        if (str_starts_with($packed, "\x20\x02")) {
            return substr($packed, 2, 4);
        }

        return null;
    }

    /**
     * @param string $packed in_addr or in_addr6
     * @param string $range  CIDR notation
     */
    protected function _isAddressInRange(string $packed, string $range): bool
    {
        [$network, $bits] = explode('/', $range);
        $packedNetwork = inet_pton($network);
        if ($packedNetwork === false || strlen($packedNetwork) !== strlen($packed)) {
            return false;
        }

        $bits = (int) $bits;
        $bytes = intdiv($bits, 8);
        if (strncmp($packed, $packedNetwork, $bytes) !== 0) {
            return false;
        }

        $remainder = $bits % 8;
        if ($remainder === 0) {
            return true;
        }

        $mask = chr((0xFF << (8 - $remainder)) & 0xFF);
        return ($packed[$bytes] & $mask) === ($packedNetwork[$bytes] & $mask);
    }

    /**
     * Check whether the URL is covered by one of the trusted URL prefixes
     *
     * @param  string $url
     * @return bool
     */
    protected function _isAllowedByPrefix($url)
    {
        foreach ($this->_getAllowedUrlPrefixes() as $prefix) {
            if ($this->_urlMatchesPrefix((string) $url, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check whether the URL falls under the given prefix
     *
     * A plain string prefix comparison is not enough: "http://files.example.lan" would also
     * match "http://files.example.lan.attacker.test/" and "http://files.example.lan@169.254.169.254/",
     * and a trusted prefix skips the address check. The scheme, credentials, host and port
     * must therefore match exactly, and only the path and query are compared as a string
     * prefix. Dot segments are rejected so that a path prefix cannot be left with "../".
     */
    protected function _urlMatchesPrefix(string $url, string $prefix): bool
    {
        $urlParts = parse_url($url);
        $prefixParts = parse_url($prefix);
        if (!is_array($urlParts) || !is_array($prefixParts)
            || !isset($urlParts['scheme'], $urlParts['host'], $prefixParts['scheme'], $prefixParts['host'])
        ) {
            return false;
        }

        $scheme = strtolower($urlParts['scheme']);
        if ($scheme !== strtolower($prefixParts['scheme'])
            || strtolower($urlParts['host']) !== strtolower($prefixParts['host'])
            || ($urlParts['user'] ?? null) !== ($prefixParts['user'] ?? null)
            || ($urlParts['pass'] ?? null) !== ($prefixParts['pass'] ?? null)
        ) {
            return false;
        }

        $defaultPort = $scheme === 'https' ? 443 : 80;
        if (($urlParts['port'] ?? $defaultPort) !== ($prefixParts['port'] ?? $defaultPort)) {
            return false;
        }

        $urlPath = ($urlParts['path'] ?? '') === '' ? '/' : $urlParts['path'];
        foreach (explode('/', rawurldecode($urlPath)) as $segment) {
            if ($segment === '.' || $segment === '..') {
                return false;
            }
        }

        $prefixPath = ($prefixParts['path'] ?? '') === '' ? '/' : $prefixParts['path'];
        $urlRest = $urlPath . (isset($urlParts['query']) ? '?' . $urlParts['query'] : '');
        $prefixRest = $prefixPath . (isset($prefixParts['query']) ? '?' . $prefixParts['query'] : '');

        return str_starts_with($urlRest, $prefixRest);
    }

    /**
     * @param array $urlProp result of _validateUrl()
     */
    protected function _getUrlOrigin(array $urlProp): string
    {
        return $urlProp['scheme'] . '://' . $urlProp['host'] . (isset($urlProp['port']) ? ':' . $urlProp['port'] : '');
    }

    /**
     * Tell the merchant, once a day per destination, that a link download was refused
     *
     * The refusal otherwise only reaches the customer as a failed download. Only the origin
     * is reported, never the full URL, which may carry a signed token.
     *
     * @param array $urlProp result of _validateUrl()
     */
    protected function _reportBlockedUrl(array $urlProp, string $address, string $addressClass): void
    {
        try {
            $origin = $this->_getUrlOrigin($urlProp);
            Mage::log(
                sprintf('Downloadable link URL refused for %s: resolves to %s address %s', $origin, $addressClass, $address),
                Level::Warning,
            );

            $cacheKey = 'downloadable_link_url_refused_' . md5($origin);
            if (Mage::app()->loadCache($cacheKey)) {
                return;
            }

            Mage::app()->saveCache('1', $cacheKey, [], 86400);

            /** @var Mage_AdminNotification_Model_Inbox $inbox */
            $inbox = Mage::getModel('adminnotification/inbox');
            $inbox->addMajor(
                Mage::helper('downloadable')->__('A downloadable product link could not be fetched from %s', $origin),
                Mage::helper('downloadable')->__(
                    'Customers could not download a URL type link or sample because %s resolves to %s, which is a %s network address. If this host is yours and trusted, add its URL prefix to System > Configuration > Catalog > Downloadable Product Options > Trusted Link URL Prefixes.',
                    $origin,
                    $address,
                    $addressClass,
                ),
            );
        } catch (Throwable $throwable) {
            Mage::logException($throwable);
        }
    }

    /**
     * Retrieve the configured trusted download URL prefixes
     *
     * @return array
     */
    protected function _getAllowedUrlPrefixes()
    {
        $allowedPrefixes = (string) Mage::getStoreConfig(self::XML_PATH_LINK_URL_ALLOWED_PREFIXES);
        if (trim($allowedPrefixes) === '') {
            return [];
        }

        return array_values(array_filter(array_map(
            trim(...),
            preg_split('/[\r\n,]+/', $allowedPrefixes),
        )));
    }

    /**
     * Retrieve file size in bytes
     *
     * @throws Mage_Core_Exception
     */
    public function getFilesize()
    {
        $handle = $this->_getHandle();
        if ($this->_linkType == self::LINK_TYPE_FILE) {
            return $handle->streamStat('size');
        }

        if ($this->_linkType == self::LINK_TYPE_URL && isset($this->_urlHeaders['content-length'])) {
            return $this->_urlHeaders['content-length'];
        }

        return null;
    }

    /**
     * @return array|string
     * @throws Exception
     */
    public function getContentType()
    {
        $this->_getHandle();
        if ($this->_linkType == self::LINK_TYPE_FILE) {
            if (function_exists('mime_content_type') && ($contentType = mime_content_type($this->_resourceFile))) {
                return $contentType;
            }

            return Mage::helper('downloadable/file')->getFileType($this->_resourceFile);
        }

        if ($this->_linkType == self::LINK_TYPE_URL && isset($this->_urlHeaders['content-type'])) {
            $contentType = explode('; ', $this->_urlHeaders['content-type']);
            return $contentType[0];
        }

        return $this->_contentType;
    }

    /**
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     * @return array|string
     * @throws Exception
     */
    public function getFilename()
    {
        $this->_getHandle();
        if ($this->_linkType == self::LINK_TYPE_FILE) {
            return pathinfo($this->_resourceFile, PATHINFO_BASENAME);
        }

        if ($this->_linkType == self::LINK_TYPE_URL) {
            if (isset($this->_urlHeaders['content-disposition'])) {
                $contentDisposition = explode('; ', $this->_urlHeaders['content-disposition']);
                if (!empty($contentDisposition[1]) && str_contains($contentDisposition[1], 'filename=')) {
                    return substr($contentDisposition[1], 9);
                }
            }

            if ($fileName = @pathinfo($this->_resourceFile, PATHINFO_BASENAME)) {
                return $fileName;
            }
        }

        return $this->_fileName;
    }

    /**
     * Set resource file for download
     *
     * @param  string              $resourceFile
     * @param  self::LINK_TYPE_*   $linkType
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setResource($resourceFile, $linkType = self::LINK_TYPE_FILE)
    {
        if (self::LINK_TYPE_FILE == $linkType) {
            //check LFI protection
            /** @var Mage_Core_Helper_Data $helper */
            $helper = Mage::helper('core');
            $helper->checkLfiProtection($resourceFile);
        }

        $this->_resourceFile    = $resourceFile;
        $this->_linkType        = $linkType;

        return $this;
    }

    /**
     * Retrieve Http Request Object
     *
     * @return Mage_Core_Controller_Request_Http
     */
    public function getHttpRequest()
    {
        return Mage::app()->getFrontController()->getRequest();
    }

    /**
     * Retrieve Http Response Object
     *
     * @return Mage_Core_Controller_Response_Http
     */
    public function getHttpResponse()
    {
        return Mage::app()->getFrontController()->getResponse();
    }

    /**
     * @throws Mage_Core_Exception
     */
    public function output()
    {
        $handle = $this->_getHandle();
        if ($this->_linkType == self::LINK_TYPE_FILE) {
            while ($buffer = $handle->streamRead()) {
                print $buffer;
            }
        } elseif ($this->_linkType == self::LINK_TYPE_URL) {
            while (!feof($handle)) {
                print fgets($handle, 1024);
            }
        }
    }

    /**
     * Use Content-Disposition: attachment
     *
     * @param  mixed $store
     * @return bool
     */
    public function getContentDisposition($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_CONTENT_DISPOSITION, $store);
    }
}
