<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

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

                $allowedPrefixes = $this->_getAllowedUrlPrefixes();
                $isAllowedByPrefix = $allowedPrefixes && $this->_isAllowedByPrefix($this->_resourceFile);
                if ($allowedPrefixes && !$isAllowedByPrefix) {
                    Mage::throwException(Mage::helper('downloadable')->__('The download URL is not allowed by the configured URL prefix whitelist.'));
                }

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
                $address = $this->_getAllowedAddress($urlProp['host'], !$isAllowedByPrefix);
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
                        if ($key == 'set-cookie') {
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
     * Unless the URL was explicitly whitelisted, every address the host resolves to must be
     * publicly routable, so that loopback, private, link-local (cloud instance metadata) and
     * other reserved destinations cannot be reached from the server.
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
                if (!$this->_isPublicAddress($address)) {
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
        return (bool) filter_var(
            $address,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,
        );
    }

    /**
     * Check whether the URL matches one of the configured allowed prefixes
     *
     * @param  string $url
     * @return bool
     */
    protected function _isAllowedByPrefix($url)
    {
        foreach ($this->_getAllowedUrlPrefixes() as $prefix) {
            if (str_starts_with($url, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve the configured allowed download URL prefixes
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
            'trim',
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
