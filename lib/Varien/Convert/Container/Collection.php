<?php

/**
 * @category   Varien
 * @package    Varien_Convert
 */

/**
 * Convert component collection
 *
 * @category   Varien
 * @package    Varien_Convert
 */
class Varien_Convert_Container_Collection
{
    protected $_items = [];
    protected $_defaultClass = 'Varien_Convert_Container_Generic';

    public function setDefaultClass($className)
    {
        $this->_defaultClass = $className;
        return $this;
    }

    public function addItem($name, Varien_Convert_Container_Interface $item)
    {
        if (is_null($name)) {
            if ($item->getName()) {
                $name = $item->getName();
            } else {
                $name = count($this->_items);
            }
        }

        $this->_items[$name] = $item;

        return $item;
    }

    public function getItem($name)
    {
        if (!isset($this->_items[$name])) {
            $this->addItem($name, new $this->_defaultClass());
        }
        return $this->_items[$name];
    }

    public function hasItem($name)
    {
        return isset($this->_items[$name]);
    }
}
