<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_XmlConnect_Model_Theme
{
    protected $_file;
    protected $_xml;
    protected $_conf;

    /**
     * Load Theme xml from $file
     *
     * @param string $file
     * @throws Mage_Core_Exception
     * @return void
     */
    public function __construct($file)
    {
        $this->_file = $file;
        if (!file_exists($file)) {
            Mage::throwException(Mage::helper('xmlconnect')->__('File doesn\'t exist "%s".', $file));
        }
        if (!is_readable($file)) {
            Mage::throwException(Mage::helper('xmlconnect')->__('Can\'t read file "%s".', $file));
        }
        $text = file_get_contents($file);
        try {
            $this->_xml = simplexml_load_string($text);
        } catch (Exception $e) {
            Mage::throwException(Mage::helper('xmlconnect')->__('Can\'t load XML.'));
        }
        if (empty($this->_xml)) {
            Mage::throwException(Mage::helper('xmlconnect')->__('Invalid XML.'));
        }
        $this->_conf = $this->_xmlToArray($this->_xml->configuration);
        $this->_conf = $this->_conf['configuration'];
        if (!is_array($this->_conf)) {
            Mage::throwException(Mage::helper('xmlconnect')->__('Wrong theme format.'));
        }
    }

    /**
     * Get theme xml as array
     * 
     * @param array $xml
     * @return array
     */
    protected function _xmlToArray($xml)
    {
        $result = array();
        foreach ($xml as $key => $value) {
            if (count($value)) {
                $result[$key] = $this->_xmlToArray($value);
            } else {
                $result[$key] = (string) $value;
            }
        }
        return $result;
    }

    /**
     * Getter for theme name
     *
     * @return string
     */
    public function getName()
    {
        return (string) $this->_xml->manifest->name;
    }

    /**
     * Getter for theme Label
     *
     * @return string
     */
    public function getLabel()
    {
        return (string) $this->_xml->manifest->label;
    }

    /**
     * Load data (flat array) for Varien_Data_Form
     *
     * @return array
     */
    public function getFormData()
    {
        return $this->_flatArray($this->_conf, 'conf');
    }

    /**
     * Load data (flat array) for Varien_Data_Form
     *
     * @param array $subtree
     * @param string $prefix
     * @return array
     */
    protected function _flatArray($subtree, $prefix=null)
    {
        $result = array();
        foreach ($subtree as $key => $value) {
            if (is_null($prefix)) {
                $name = $key;
            } else {
                $name = $prefix . '[' . $key . ']';
            }

            if (is_array($value)) {
                $result = array_merge($result, $this->_flatArray($value, $name));
            } else {
                $result[$name] = $value;
            }
        }
        return $result;
    }

    /**
     * Validate input Array, recursive
     *
     * @param array $data
     * @param array $xml
     * @return array
     */
    protected function _validateFormInput($data, $xml=NULL)
    {
        $root = false;
        $result = array();
        if (is_null($xml)) {
            $root = true;
            $data = array('configuration' => $data);
            $xml = $this->_xml->configuration;
        }
        foreach ($xml as $key => $value) {
            if (isset($data[$key])) {
                if (is_array($data[$key])) {
                    $result[$key] = $this->_validateFormInput($data[$key], $value);
                } else {
                    $result[$key] = $data[$key];
                }
            }
        }
        if ($root) {
            $result = $result['configuration'];
        }
        return $result;
    }

    /**
     * Build XML object recursively from $data array
     *
     * @param SimpleXMLElement $parent
     * @param array $data
     * @return void
     */
    protected function _buildRecursive($parent, $data)
    {
        foreach ($data as $key=>$value) {
            if (is_array($value)) {
                $this->_buildRecursive($parent->addChild($key), $value);
            } else {
                $parent->addChild($key, $value);
            }
        }
    }

    /**
     * Import data into theme form $data array, and save XML to file
     *
     * @param array $data
     * @return void
     */
    public function importAndSaveData($data)
    {
        $xml = new SimpleXMLElement('<theme>'.$this->_xml->manifest->asXML().'</theme>');
        $this->_buildRecursive($xml->addChild('configuration'), $this->_validateFormInput($data));
        clearstatcache();
        if (is_writeable($this->_file)) {
            file_put_contents($this->_file, $xml->asXML());
        } else {
            Mage::throwException(Mage::helper('xmlconnect')->__('Can\'t write to file "%s".', $this->_file));
        }
    }
}
