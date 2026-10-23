<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Classification of the network addresses outbound requests may be sent to
 *
 * Shared by every feature that fetches a URL an admin or merchant configured (downloadable
 * link URLs, Dataflow HTTP imports), so that they refuse the same destinations.
 *
 * @package    Mage_Core
 */
class Mage_Core_Helper_Network extends Mage_Core_Helper_Abstract
{
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
    public const RESTRICTED_RANGES = [
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
     * Private-use destinations (RFC 1918, carrier-grade NAT, unique local IPv6), which each
     * feature allows or refuses through its own setting
     */
    public const PRIVATE_RANGES = [
        '10.0.0.0/8',
        '100.64.0.0/10',
        '172.16.0.0/12',
        '192.168.0.0/16',
        'fc00::/7',
    ];

    protected $_moduleName = 'Mage_Core';

    /**
     * Classify an IP address as public, private-use or restricted
     *
     * IPv6 addresses that embed an IPv4 address (IPv4-mapped, IPv4-compatible, NAT64 and
     * 6to4) are classified by the embedded IPv4 address. Anything that is not a valid IP
     * address is restricted.
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
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
     * Whether an address of the given class may be fetched
     *
     * @param string $class one of the ADDRESS_* constants
     */
    public function isAddressClassAllowed(string $class, bool $allowPrivateNetwork): bool
    {
        return $class === self::ADDRESS_PUBLIC
            || ($class === self::ADDRESS_PRIVATE && $allowPrivateNetwork);
    }

    /**
     * Retrieve all IPv4 and IPv6 addresses the given host resolves to
     *
     * An IP address literal (with or without IPv6 brackets) is returned as it is.
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     * @return list<string>
     */
    public function resolveHost(string $host): array
    {
        $host = trim($host, '[]');
        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return [$host];
        }

        $addresses = gethostbynamel($host);
        if ($addresses === false) {
            $addresses = [];
        }

        $records = @dns_get_record($host, DNS_AAAA);
        foreach (is_array($records) ? $records : [] as $record) {
            if (isset($record['ipv6']) && is_string($record['ipv6']) && $record['ipv6'] !== '') {
                $addresses[] = $record['ipv6'];
            }
        }

        return array_values($addresses);
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
}
