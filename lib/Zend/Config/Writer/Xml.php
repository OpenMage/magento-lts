<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Xml.php 12221 2008-10-31 20:32:43Z dasprid $
 */

/**
 * @see Zend_Config_Writer
 */
#require_once 'Zend/Config/Writer.php';

/**
 * @category   Zend
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Config_Writer_Xml extends Zend_Config_Writer
{
    /**
     * Filename to write to
     *
     * @var string
     */
    protected $_filename = null;
    
    /**
     * Set the target filename
     *
     * @param  string $filename
     * @return Zend_Config_Writer_Xml
     */
    public function setFilename($filename)
    {
        $this->_filename = $filename;
        
        return $this;
    }
    
    /**
     * Defined by Zend_Config_Writer
     *
     * @param  string      $filename
     * @param  Zend_Config $config
     * @throws Zend_Config_Exception When filename was not set
     * @throws Zend_Config_Exception When filename is not writable
     * @return void
     */
    public function write($filename = null, Zend_Config $config = null)
    {
        if ($filename !== null) {
            $this->setFilename($filename);
        }
        
        if ($config !== null) {
            $this->setConfig($config);
        }
        
        if ($this->_filename === null) {
            #require_once 'Zend/Config/Exception.php';
            throw new Zend_Config_Exception('No filename was set');
        }
        
        if ($this->_config === null) {
            #require_once 'Zend/Config/Exception.php';
            throw new Zend_Config_Exception('No config was set');
        }
        
        $xml         = new SimpleXMLElement('<zend-config/>');
        $extends     = $this->_config->getExtends();
        $sectionName = $this->_config->getSectionName();
        
        if (is_string($sectionName)) {
            $child = $xml->addChild($sectionName);

            $this->_addBranch($this->_config, $child);
        } else {
            foreach ($this->_config as $sectionName => $data) {
                if (!($data instanceof Zend_Config)) {
                    continue;
                }
            
                $child = $xml->addChild($sectionName);
                
                if (isset($extends[$sectionName])) {
                    $child->addAttribute('extends', $extends[$sectionName]);
                }
    
                $this->_addBranch($data, $child);
            }
        }
                
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        
        $xmlString = $dom->saveXML();
       
        $result = @file_put_contents($this->_filename, $xmlString);
        
        if ($result === false) {
            #require_once 'Zend/Config/Exception.php';
            throw new Zend_Config_Exception('Could not write to file "' . $this->_filename . '"');
        }
    }
    
    /**
     * Add a branch to an XML object recursively
     *
     * @param  Zend_Config      $config
     * @param  SimpleXMLElement $xml
     * @return void
     */
    protected function _addBranch(Zend_Config $config, SimpleXMLElement $xml)
    {
        foreach ($config as $key => $value) {
            if ($value instanceof Zend_Config) {
                $child = $xml->addChild($key);

                $this->_addBranch($value, $child);
            } else {
                $xml->addChild($key, (string) $value);
            }
        }
    }
}
