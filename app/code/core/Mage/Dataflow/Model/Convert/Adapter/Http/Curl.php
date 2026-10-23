<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * Convert CURL HTTP adapter
 *
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Convert_Adapter_Http_Curl extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
    public const XML_PATH_ALLOWED_URL_PREFIXES = 'system/dataflow_url_fetch/allowed_url_prefixes';

    public const XML_PATH_ALLOW_PRIVATE_NETWORK = 'system/dataflow_url_fetch/allow_private_network';

    public function load()
    {
        // we expect <var name="uri">http://...</var>
        $uri = $this->getVar('uri');
        $this->_validateUri($uri);

        // use Varien curl adapter
        $http = new Varien_Http_Adapter_Curl();
        $this->_restrictProtocols($http);
        $url = $this->_restrictDestination($http, (string) $uri);

        // send GET request
        $http->write('GET', $url);

        // read the remote file
        $data = $http->read();

        $http->close();

        $data = preg_split('/^\r?$/m', $data, 2);
        $data = trim($data[1]);

        // save contents into container
        $this->setData($data);

        return $this;
    }

    public function save()
    {
        // no save implemented
        return $this;
    }

    /**
     * @throws Mage_Dataflow_Model_Convert_Exception
     */
    protected function _validateUri($uri): void
    {
        /** @var Mage_Core_Helper_Validate $validator */
        $validator = Mage::helper('core/validate');
        if ($validator->validateUrl(value: $uri, protocols: ['http', 'https'])->count() > 0) {
            throw $this->addException(
                "Expecting a valid 'uri' parameter with http or https protocol",
                Mage_Dataflow_Model_Convert_Exception::FATAL,
            );
        }

        if (!$this->_isAllowedByPrefix($uri)) {
            throw $this->addException(
                "The 'uri' parameter is not allowed by the configured URL prefix whitelist",
                Mage_Dataflow_Model_Convert_Exception::FATAL,
            );
        }
    }

    protected function _isAllowedByPrefix(string $uri): bool
    {
        $allowedPrefixes = $this->_getAllowedUrlPrefixes();
        if (!$allowedPrefixes) {
            return true;
        }

        foreach ($allowedPrefixes as $prefix) {
            if ($this->_uriMatchesPrefix($uri, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check whether the URI falls under the given allowed prefix
     *
     * A plain string prefix comparison is not enough: "https://feeds.example.com" would
     * also match "https://feeds.example.com.attacker.test/" and
     * "https://feeds.example.com@169.254.169.254/". The scheme, credentials, host and
     * port must therefore match the prefix exactly, and only the path and query are
     * compared as a string prefix. Dot segments are rejected so that a path prefix
     * cannot be left with "../" once the URI is normalised.
     */
    protected function _uriMatchesPrefix(string $uri, string $prefix): bool
    {
        $uriParts = parse_url($uri);
        $prefixParts = parse_url($prefix);
        if (!is_array($uriParts) || !is_array($prefixParts)
            || !isset($uriParts['scheme'], $uriParts['host'], $prefixParts['scheme'], $prefixParts['host'])
        ) {
            return false;
        }

        $scheme = strtolower($uriParts['scheme']);
        if ($scheme !== strtolower($prefixParts['scheme'])
            || strtolower($uriParts['host']) !== strtolower($prefixParts['host'])
            || ($uriParts['user'] ?? null) !== ($prefixParts['user'] ?? null)
            || ($uriParts['pass'] ?? null) !== ($prefixParts['pass'] ?? null)
        ) {
            return false;
        }

        $defaultPort = $scheme === 'https' ? 443 : 80;
        if (($uriParts['port'] ?? $defaultPort) !== ($prefixParts['port'] ?? $defaultPort)) {
            return false;
        }

        $uriPath = ($uriParts['path'] ?? '') === '' ? '/' : $uriParts['path'];
        foreach (explode('/', rawurldecode($uriPath)) as $segment) {
            if ($segment === '.' || $segment === '..') {
                return false;
            }
        }

        $prefixPath = ($prefixParts['path'] ?? '') === '' ? '/' : $prefixParts['path'];
        $uriRest = $uriPath . (isset($uriParts['query']) ? '?' . $uriParts['query'] : '');
        $prefixRest = $prefixPath . (isset($prefixParts['query']) ? '?' . $prefixParts['query'] : '');

        return str_starts_with($uriRest, $prefixRest);
    }

    protected function _getAllowedUrlPrefixes(): array
    {
        $allowedPrefixes = (string) Mage::getStoreConfig(self::XML_PATH_ALLOWED_URL_PREFIXES);
        if (trim($allowedPrefixes) === '') {
            return [];
        }

        return array_values(array_filter(array_map(
            trim(...),
            preg_split('/[\r\n,]+/', $allowedPrefixes),
        )));
    }

    /**
     * Refuse destinations the server must not fetch from and connect only to the checked address
     *
     * Unless a configured URL prefix covers the URI, every address its host resolves to must be
     * allowed: public addresses always, private-use addresses (RFC 1918, carrier-grade NAT,
     * unique local IPv6) unless system/dataflow_url_fetch/allow_private_network is off, and
     * loopback, link-local, cloud instance metadata and other special-purpose addresses never.
     * curl is then pinned to the checked address with CURLOPT_RESOLVE, so that a second DNS
     * answer cannot swap the destination (DNS rebinding), and redirects are not followed.
     *
     * The request URL is rebuilt from the parsed components so that curl connects to the same
     * host that was checked here.
     *
     * @return string                                the URL to request
     * @throws Mage_Dataflow_Model_Convert_Exception
     */
    protected function _restrictDestination(Varien_Http_Adapter_Curl $http, string $uri): string
    {
        $parts = parse_url($uri);
        if (!is_array($parts) || !isset($parts['scheme'], $parts['host']) || $parts['host'] === '') {
            throw $this->addException(
                "Expecting a valid 'uri' parameter with http or https protocol",
                Mage_Dataflow_Model_Convert_Exception::FATAL,
            );
        }

        $scheme = strtolower($parts['scheme']);
        $host = strtolower($parts['host']);
        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);

        $url = $scheme . '://';
        if (isset($parts['user'])) {
            $url .= $parts['user'] . (isset($parts['pass']) ? ':' . $parts['pass'] : '') . '@';
        }

        $url .= $host . (isset($parts['port']) ? ':' . $parts['port'] : '')
            . ($parts['path'] ?? '/')
            . (isset($parts['query']) ? '?' . $parts['query'] : '');

        if (defined('CURLOPT_FOLLOWLOCATION')) {
            $http->addOption(CURLOPT_FOLLOWLOCATION, false);
        }

        if ($this->_getAllowedUrlPrefixes() !== [] && $this->_isAllowedByPrefix($uri)) {
            return $url;
        }

        $addresses = $this->_resolveHost($host);
        if ($addresses === []) {
            throw $this->addException(
                "The 'uri' parameter host cannot be resolved",
                Mage_Dataflow_Model_Convert_Exception::FATAL,
            );
        }

        $network = $this->_getNetworkHelper();
        $allowPrivateNetwork = Mage::getStoreConfigFlag(self::XML_PATH_ALLOW_PRIVATE_NETWORK);
        foreach ($addresses as $address) {
            $class = $network->getAddressClass($address);
            if (!$network->isAddressClassAllowed($class, $allowPrivateNetwork)) {
                throw $this->addException(
                    sprintf(
                        "The 'uri' parameter host resolves to %s, a %s network address that may not be fetched."
                        . ' List the URL under Allowed URL Prefixes to fetch it anyway.',
                        $address,
                        $class,
                    ),
                    Mage_Dataflow_Model_Convert_Exception::FATAL,
                );
            }
        }

        $hostName = trim($host, '[]');
        if (filter_var($hostName, FILTER_VALIDATE_IP) === false) {
            $address = reset($addresses);
            $http->addOption(CURLOPT_RESOLVE, [
                sprintf('%s:%d:%s', $hostName, $port, str_contains($address, ':') ? '[' . $address . ']' : $address),
            ]);
        }

        return $url;
    }

    /**
     * @return list<string>
     */
    protected function _resolveHost(string $host): array
    {
        return $this->_getNetworkHelper()->resolveHost($host);
    }

    protected function _getNetworkHelper(): Mage_Core_Helper_Network
    {
        /** @var Mage_Core_Helper_Network $helper */
        $helper = Mage::helper('core/network');

        return $helper;
    }

    protected function _restrictProtocols(Varien_Http_Adapter_Curl $http): void
    {
        $protocols = CURLPROTO_HTTP | CURLPROTO_HTTPS;

        if (defined('CURLOPT_PROTOCOLS')) {
            $http->addOption(CURLOPT_PROTOCOLS, $protocols);
        }

        if (defined('CURLOPT_REDIR_PROTOCOLS')) {
            $http->addOption(CURLOPT_REDIR_PROTOCOLS, $protocols);
        }
    }
}
