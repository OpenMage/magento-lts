<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Xml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
