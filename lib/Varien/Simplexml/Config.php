<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Simplexml
 */

/**
 * Base class for simplexml based configurations
 *
 * @package    Varien_Simplexml
 */
class Varien_Simplexml_Config
{
    /**
     * Configuration xml
     *
     * @var Varien_Simplexml_Element|SimpleXMLElement
     */
    protected $_xml = null;

    /**
     * @var string
     */
    protected $_cacheId = null;

    /**
     * @var array
     */
    protected $_cacheTags = [];

    /**
     * @var int
     */
    protected $_cacheLifetime = null;

    /**
     * @var string|false|null
     */
    protected $_cacheChecksum = false;

    /**
     * @var bool
     */
    protected $_cacheSaved = false;

    /**
     * Cache resource object
     *
     * @var Varien_Simplexml_Config_Cache_Abstract
     */
    protected $_cache = null;

    /**
     * Class name of simplexml elements for this configuration
     *
     * @var string
     */
    protected $_elementClass = 'Varien_Simplexml_Element';

    /**
     * Xpath describing nodes in configuration that need to be extended
     *
     * @example <allResources extends="/config/modules//resource"/>
     */
    protected $_xpathExtends = '//*[@extends]';

    /**
     * Constructor
     *
     * Initializes XML for this configuration
     *
     * @see self::setXml
     * @param string|Varien_Simplexml_Element $sourceData
     */
    public function __construct($sourceData = null)
    {
        if (is_null($sourceData)) {
            return;
        }
        if ($sourceData instanceof Varien_Simplexml_Element) {
            $this->setXml($sourceData);
        } elseif (is_string($sourceData) && !empty($sourceData)) {
            if (strlen($sourceData) < 1000 && is_readable($sourceData)) {
                $this->loadFile($sourceData);
            } else {
                $this->loadString($sourceData);
            }
        }
    }

    /**
     * Sets xml for this configuration
     *
     * @return $this
     */
    public function setXml(Varien_Simplexml_Element $node)
    {
        $this->_xml = $node;
        return $this;
    }

    /**
     * Returns node found by the $path
     *
     * @see     Varien_Simplexml_Element::descend
     * @param   string $path
     * @return  Varien_Simplexml_Element|false
     */
    public function getNode($path = null)
    {
        if (!$this->_xml instanceof Varien_Simplexml_Element) {
            return false;
        } elseif ($path === null) {
            return $this->_xml;
        } else {
            return $this->_xml->descend($path);
        }
    }

    /**
     * Returns nodes found by xpath expression
     *
     * @param string $xpath
     * @return Varien_Simplexml_Element[]|false
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    public function getXpath($xpath)
    {
        if (empty($this->_xml)) {
            return false;
        }

        if (!$result = @$this->_xml->xpath($xpath)) {
            return false;
        }

        return $result;
    }

    /**
     * @param Varien_Simplexml_Config_Cache_Abstract $cache
     * @return $this
     */
    public function setCache($cache)
    {
        $this->_cache = $cache;
        return $this;
    }

    /**
     * @return Varien_Simplexml_Config_Cache_Abstract
     */
    public function getCache()
    {
        return $this->_cache;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setCacheSaved($flag)
    {
        $this->_cacheSaved = $flag;
        return $this;
    }

    /**
     * @return bool
     */
    public function getCacheSaved()
    {
        return $this->_cacheSaved;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setCacheId($id)
    {
        $this->_cacheId = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getCacheId()
    {
        return $this->_cacheId;
    }

    /**
     * @param array $tags
     * @return $this
     */
    public function setCacheTags($tags)
    {
        $this->_cacheTags = $tags;
        return $this;
    }

    /**
     * @return array
     */
    public function getCacheTags()
    {
        return $this->_cacheTags;
    }

    /**
     * @param int $lifetime
     * @return $this
     */
    public function setCacheLifetime($lifetime)
    {
        $this->_cacheLifetime = $lifetime;
        return $this;
    }

    /**
     * @return int
     */
    public function getCacheLifetime()
    {
        return $this->_cacheLifetime;
    }

    /**
     * @param string|null $data
     * @return $this
     */
    public function setCacheChecksum($data)
    {
        if (is_null($data)) {
            $this->_cacheChecksum = null;
        } elseif (false === $data || 0 === $data) {
            $this->_cacheChecksum = false;
        } else {
            $this->_cacheChecksum = md5($data);
        }
        return $this;
    }

    /**
     * @param string|false $data
     * @return $this
     */
    public function updateCacheChecksum($data)
    {
        if (false === $this->getCacheChecksum()) {
            return $this;
        }
        if (false === $data || 0 === $data) {
            $this->_cacheChecksum = false;
        } else {
            $this->setCacheChecksum($this->getCacheChecksum() . ':' . $data);
        }
        return $this;
    }

    /**
     * @return string|false|null
     */
    public function getCacheChecksum()
    {
        return $this->_cacheChecksum;
    }

    /**
     * @return string
     */
    public function getCacheChecksumId()
    {
        return $this->getCacheId() . '__CHECKSUM';
    }

    /**
     * @return bool
     */
    public function fetchCacheChecksum()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function validateCacheChecksum()
    {
        $newChecksum = $this->getCacheChecksum();
        if (false === $newChecksum) {
            return false;
        }
        if (is_null($newChecksum)) {
            return true;
        }
        $cachedChecksum = $this->getCache()->load($this->getCacheChecksumId());
        return $newChecksum === false && $cachedChecksum === false || $newChecksum === $cachedChecksum;
    }

    /**
     * @return bool
     */
    public function loadCache()
    {
        if (!$this->validateCacheChecksum()) {
            return false;
        }

        $xmlString = $this->_loadCache($this->getCacheId());
        if ($xmlString) {
            $xml = simplexml_load_string($xmlString, $this->_elementClass);
            if ($xml) {
                $this->_xml = $xml;
                $this->setCacheSaved(true);
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $tags
     * @return $this
     */
    public function saveCache($tags = null)
    {
        if ($this->getCacheSaved()) {
            return $this;
        }
        if (false === $this->getCacheChecksum()) {
            return $this;
        }

        if (is_null($tags)) {
            $tags = $this->_cacheTags;
        }

        if (!is_null($this->getCacheChecksum())) {
            $this->_saveCache($this->getCacheChecksum(), $this->getCacheChecksumId(), $tags, $this->getCacheLifetime());
        }

        $xmlString = $this->getXmlString();
        $this->_saveCache($xmlString, $this->getCacheId(), $tags, $this->getCacheLifetime());

        $this->setCacheSaved(true);

        return $this;
    }

    /**
     * Return Xml of node as string
     *
     * @return string
     */
    public function getXmlString()
    {
        return $this->getNode()->asNiceXml('', false);
    }

    /**
     * @return $this
     */
    public function removeCache()
    {
        $this->_removeCache($this->getCacheId());
        $this->_removeCache($this->getCacheChecksumId());
        return $this;
    }

    /**
     * @param string $id
     * @return bool
     */
    protected function _loadCache($id)
    {
        return $this->getCache()->load($id);
    }

    /**
     * @param string $data
     * @param string $id
     * @param array $tags
     * @param int|bool $lifetime
     * @return bool
     */
    protected function _saveCache($data, $id, $tags = [], $lifetime = false)
    {
        return $this->getCache()->save($data, $id, $tags, $lifetime);
    }

    /**
     * @todo check this, as there are no caches that implement remove() method
     * @param string $id
     * @return mixed
     */
    protected function _removeCache($id)
    {
        return $this->getCache()->remove($id);
    }

    /**
     * Imports XML file
     *
     * @param string $filePath
     * @return bool
     */
    public function loadFile($filePath)
    {
        if (!is_readable($filePath)) {
            //throw new Exception('Can not read xml file '.$filePath);
            return false;
        }

        $fileData = file_get_contents($filePath);
        $fileData = $this->processFileData($fileData);
        $success = $this->loadString($fileData, $this->_elementClass);

        if ($success === false) {
            Mage::throwException('Cannot parse XML file at ' . $filePath);
        }
        return $success;
    }

    /**
     * Imports XML string
     *
     * @param  string $string
     * @return bool
     */
    public function loadString($string)
    {
        if (is_string($string)) {
            $xml = simplexml_load_string($string, $this->_elementClass);

            if ($xml instanceof Varien_Simplexml_Element) {
                $this->_xml = $xml;
                return true;
            }
        } else {
            Mage::logException(new InvalidArgumentException('"$string" parameter for simplexml_load_string is not a string'));
        }
        return false;
    }

    /**
     * Imports DOM node
     *
     * @param DOMNode $dom
     * @return bool
     */
    public function loadDom($dom)
    {
        $xml = simplexml_import_dom($dom, $this->_elementClass);

        if ($xml) {
            $this->_xml = $xml;
            return true;
        }

        return false;
    }

    /**
     * Create node by $path and set its value.
     *
     * @param string $path separated by slashes
     * @param string $value
     * @param bool $overwrite
     * @return $this
     */
    public function setNode($path, $value, $overwrite = true)
    {
        $xml = $this->_xml->setNode($path, $value, $overwrite);
        return $this;
    }

    /**
     * Process configuration xml
     *
     * @return $this
     */
    public function applyExtends()
    {
        $targets = $this->getXpath($this->_xpathExtends);
        if (!$targets) {
            return $this;
        }

        foreach ($targets as $target) {
            $sources = $this->getXpath((string) $target['extends']);
            if ($sources) {
                foreach ($sources as $source) {
                    $target->extend($source);
                }
            }
        }
        return $this;
    }

    /**
     * Stub method for processing file data right after loading the file text
     *
     * @param string $text
     * @return string
     */
    public function processFileData($text)
    {
        return $text;
    }

    /**
     * @param bool $overwrite
     * @return $this
     */
    public function extend(Varien_Simplexml_Config $config, $overwrite = true)
    {
        $this->getNode()->extend($config->getNode(), $overwrite);
        return $this;
    }
}
