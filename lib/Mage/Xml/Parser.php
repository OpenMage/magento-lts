<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Xml
 */
class Mage_Xml_Parser
{
    protected $_dom = null;

    protected $_currentDom;

    protected $_content = [];

    public function __construct()
    {
        $this->_dom = new DOMDocument();
        $this->_currentDom = $this->_dom;
    }

    public function getDom()
    {
        return $this->_dom;
    }

    protected function _getCurrentDom()
    {
        return $this->_currentDom;
    }

    protected function _setCurrentDom($node)
    {
        $this->_currentDom = $node;
        return $this;
    }

    public function xmlToArray()
    {
        $this->_content = $this->_xmlToArray();
        return $this->_content;
    }

    protected function _xmlToArray($currentNode = false)
    {
        if (!$currentNode) {
            $currentNode = $this->getDom();
        }

        $content = [];
        foreach ($currentNode->childNodes as $node) {
            switch ($node->nodeType) {
                case XML_ELEMENT_NODE:
                    $value = null;
                    if ($node->hasChildNodes()) {
                        $value = $this->_xmlToArray($node);
                    }

                    $attributes = [];
                    if ($node->hasAttributes()) {
                        foreach ($node->attributes as $attribute) {
                            $attributes += [$attribute->name => $attribute->value];
                        }

                        $value = ['_value' => $value, '_attribute' => $attributes];
                    }

                    if (isset($content[$node->nodeName])) {
                        if (!isset($content[$node->nodeName][0]) || !is_array($content[$node->nodeName][0])) {
                            $oldValue = $content[$node->nodeName];
                            $content[$node->nodeName] = [];
                            $content[$node->nodeName][] = $oldValue;
                        }

                        $content[$node->nodeName][] = $value;
                    } else {
                        $content[$node->nodeName] = $value;
                    }

                    break;
                case XML_TEXT_NODE:
                    if (trim($node->nodeValue)) {
                        $content = $node->nodeValue;
                    }

                    break;
            }
        }

        return $content;
    }

    public function load($file)
    {
        $this->getDom()->load($file);
        return $this;
    }

    public function loadXML($string)
    {
        $this->getDom()->loadXML($string);
        return $this;
    }
}
