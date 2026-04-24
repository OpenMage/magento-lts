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
            if (str_starts_with($uri, $prefix)) {
                return true;
            }
        }

        return false;
    }

    protected function _getAllowedUrlPrefixes(): array
    {
        $allowedPrefixes = (string) Mage::getStoreConfig(self::XML_PATH_ALLOWED_URL_PREFIXES);
        if (trim($allowedPrefixes) === '') {
            return [];
        }

        return array_values(array_filter(array_map(
            'trim',
            preg_split('/[\r\n,]+/', $allowedPrefixes),
        )));
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
