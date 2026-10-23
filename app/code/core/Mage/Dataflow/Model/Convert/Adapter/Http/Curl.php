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

    public function load()
    {
        // we expect <var name="uri">http://...</var>
        $uri = $this->getVar('uri');
        $this->_validateUri($uri);

        // use Varien curl adapter
        $http = new Varien_Http_Adapter_Curl();
        $this->_restrictProtocols($http);

        // send GET request
        $http->write('GET', $uri);

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
     * @param  mixed                                 $uri
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

        if (!$this->_isAllowedByPrefix((string) $uri)) {
            throw $this->addException(
                "The 'uri' parameter is not allowed by the configured URL prefix whitelist",
                Mage_Dataflow_Model_Convert_Exception::FATAL,
            );
        }
    }

    protected function _isAllowedByPrefix(string $uri): bool
    {
        $allowedPrefixes = $this->_getAllowedUrlPrefixes();
        if ($allowedPrefixes === []) {
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

    /**
     * @return list<string>
     */
    protected function _getAllowedUrlPrefixes(): array
    {
        $allowedPrefixes = (string) Mage::getStoreConfig(self::XML_PATH_ALLOWED_URL_PREFIXES);
        if (trim($allowedPrefixes) === '') {
            return [];
        }

        $lines = preg_split('/[\r\n,]+/', $allowedPrefixes);
        if ($lines === false) {
            return [];
        }

        return array_values(array_filter(
            array_map(trim(...), $lines),
            static fn(string $prefix): bool => $prefix !== '',
        ));
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
