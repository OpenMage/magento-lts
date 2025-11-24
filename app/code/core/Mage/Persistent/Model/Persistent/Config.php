<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Persistent
 */

/**
 * Persistent Config Model
 *
 * @package    Mage_Persistent
 */
class Mage_Persistent_Model_Persistent_Config
{
    /**
     * XML config instance for Persistent mode
     * @var null|Varien_Simplexml_Element
     */
    protected $_xmlConfig = null;

    /**
     * Path to config file
     *
     * @var string
     */
    protected $_configFilePath;

    /**
     * Set path to config file that should be loaded
     *
     * @param string $path
     * @return $this
     */
    public function setConfigFilePath($path)
    {
        $this->_configFilePath = $path;
        $this->_xmlConfig = null;
        return $this;
    }

    /**
     * Load persistent XML config
     *
     * @return Varien_Simplexml_Element
     * @throws Mage_Core_Exception
     */
    public function getXmlConfig()
    {
        if (is_null($this->_xmlConfig)) {
            $filePath = $this->_configFilePath;
            if (!is_file($filePath) || !is_readable($filePath)) {
                $io = new Varien_Io_File();
                Mage::throwException(Mage::helper('persistent')->__(
                    'Cannot load configuration from file %s.',
                    $io->getFilteredPath($filePath),
                ));
            }

            $xml = file_get_contents($filePath);
            $this->_xmlConfig = new Varien_Simplexml_Element($xml);
        }

        return $this->_xmlConfig;
    }

    /**
     * Retrieve instances that should be emulated by persistent data
     *
     * @return array
     */
    public function collectInstancesToEmulate()
    {
        $config = $this->getXmlConfig()->asArray();
        return $config['instances'];
    }

    /**
     * Run all methods declared in persistent configuration
     *
     * @return $this
     */
    public function fire()
    {
        foreach ($this->collectInstancesToEmulate() as $type => $elements) {
            if (!is_array($elements)) {
                continue;
            }

            foreach ($elements as $info) {
                if ($type === 'blocks') {
                    $this->fireOne($info, Mage::getSingleton('core/layout')->getBlock($info['name_in_layout']));
                }
            }
        }

        return $this;
    }

    /**
     * Run one method by given method info
     *
     * @param array $info
     * @param false|Mage_Core_Block_Abstract $instance
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function fireOne($info, $instance = false)
    {
        if (!$instance
            || (isset($info['block_type']) && !($instance instanceof $info['block_type']))
            || !isset($info['class'])
            || !isset($info['method'])
        ) {
            return $this;
        }

        $object     = Mage::getModel($info['class']);
        $method     = $info['method'];

        if (method_exists($object, $method)) {
            $object->$method($instance);
        } elseif (Mage::getIsDeveloperMode()) {
            if (!$object instanceof Mage_Core_Model_Abstract) {
                Mage::throwException(sprintf('Model "%s" is not defined"', $info['class']));
            } else {
                Mage::throwException(sprintf('Method "%s" is not defined in "%s"', $method, $object::class));
            }
        }

        return $this;
    }
}
