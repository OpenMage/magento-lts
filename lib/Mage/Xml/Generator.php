<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Xml
 */
class Mage_Xml_Generator
{
    protected $_dom = null;
    protected $_currentDom;

    public function __construct()
    {
        $this->_dom = new DOMDocument('1.0');
        $this->_dom->formatOutput = true;
        $this->_currentDom = $this->_dom;
    }

    /**
     * @return DOMDocument|null
     */
    public function getDom()
    {
        return $this->_dom;
    }

    /**
     * @return DOMDocument
     */
    protected function _getCurrentDom()
    {
        return $this->_currentDom;
    }

    /**
     * @param DOMElement $node
     * @return $this
     */
    protected function _setCurrentDom($node)
    {
        $this->_currentDom = $node;
        return $this;
    }

    /**
    * @param array|array[] $content
    */
    public function arrayToXml($content)
    {
        $parentNode = $this->_getCurrentDom();
        if (!$content || !count($content)) {
            return $this;
        }
        foreach ($content as $key => $item) {
            try {
                $node = $this->getDom()->createElement($key);
            } catch (DOMException $e) {
                //  echo $e->getMessage();
                var_dump($item);
                die;
            }
            $parentNode->appendChild($node);
            if (is_array($item) && isset($item['_attribute'])) {
                if (is_array($item['_value'])) {
                    if (isset($item['_value'][0])) {
                        foreach ($item['_value'] as $value) {
                            $this->_setCurrentDom($node)->arrayToXml($value);
                        }
                    } else {
                        $this->_setCurrentDom($node)->arrayToXml($item['_value']);
                    }
                } else {
                    $child = $this->getDom()->createTextNode($item['_value']);
                    $node->appendChild($child);
                }
                foreach ($item['_attribute'] as $_attributeKey => $_attributeValue) {
                    $node->setAttribute($_attributeKey, $_attributeValue);
                }
            } elseif (is_string($item)) {
                $text = $this->getDom()->createTextNode($item);
                $node->appendChild($text);
            } elseif (is_array($item) && !isset($item[0])) {
                $this->_setCurrentDom($node)->arrayToXml($item);
            } elseif (is_array($item) && isset($item[0])) {
                foreach ($item as $k => $v) {
                    $this->_setCurrentDom($node)->arrayToXml($v);
                }
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getDom()->saveXML();
    }

    /**
     * @param string $file
     * @return $this
     */
    public function save($file)
    {
        $this->getDom()->save($file);
        return $this;
    }
}
