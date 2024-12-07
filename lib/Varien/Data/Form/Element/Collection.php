<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Varien
 * @package    Varien_Data
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Form element collection
 *
 * @category   Varien
 * @package    Varien_Data
 */
class Varien_Data_Form_Element_Collection implements ArrayAccess, IteratorAggregate, Countable
{
    /**
     * Elements storage
     *
     * @var array
     */
    // phpcs:ignore Ecg.PHP.PrivateClassMember.PrivateClassMemberError
    private $_elements;

    /**
     * Elements container
     *
     * @var Varien_Data_Form_Abstract
     */
    // phpcs:ignore Ecg.PHP.PrivateClassMember.PrivateClassMemberError
    private $_container;

    /**
     * Class constructor
     *
     * @param Varien_Data_Form_Abstract $container
     */
    public function __construct($container)
    {
        $this->_elements = [];
        $this->_container = $container;
    }

    /**
     * Implementation of IteratorAggregate::getIterator()
     *
     * @return ArrayIterator
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->_elements);
    }

    /**
     * Implementation of ArrayAccess:offsetSet()
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function offsetSet($key, $value): void
    {
        $this->_elements[$key] = $value;
    }

    /**
     * Implementation of ArrayAccess:offsetGet()
     *
     * @param mixed $key
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($key)
    {
        return $this->_elements[$key];
    }

    /**
     * Implementation of ArrayAccess:offsetUnset()
     *
     * @param mixed $key
     */
    public function offsetUnset($key): void
    {
        unset($this->_elements[$key]);
    }

    /**
     * Implementation of ArrayAccess:offsetExists()
     *
     * @param mixed $key
     */
    public function offsetExists($key): bool
    {
        return isset($this->_elements[$key]);
    }

    /**
     * Add element to collection
     *
     * @todo get it straight with $after
     * @param string|false $after
     * @return Varien_Data_Form_Element_Abstract
     */
    public function add(Varien_Data_Form_Element_Abstract $element, $after = false)
    {
        // Set the Form for the node
        if ($this->_container->getForm() instanceof Varien_Data_Form) {
            $element->setContainer($this->_container);
            $element->setForm($this->_container->getForm());
        }

        if ($after === false) {
            $this->_elements[] = $element;
        } elseif ($after === '^') {
            array_unshift($this->_elements, $element);
        } elseif (is_string($after)) {
            $newOrderElements = [];
            foreach ($this->_elements as $index => $currElement) {
                if ($currElement->getId() == $after) {
                    $newOrderElements[] = $currElement;
                    $newOrderElements[] = $element;
                    $this->_elements = array_merge($newOrderElements, array_slice($this->_elements, $index + 1));
                    return $element;
                }
                $newOrderElements[] = $currElement;
            }
            $this->_elements[] = $element;
        }

        return $element;
    }

    /**
     * Sort elements by values using a user-defined comparison function
     *
     * @param mixed $callback
     * @return Varien_Data_Form_Element_Collection
     */
    public function usort($callback)
    {
        usort($this->_elements, $callback);
        return $this;
    }

    /**
     * Remove element from collection
     *
     * @param mixed $elementId
     * @return Varien_Data_Form_Element_Collection
     */
    public function remove($elementId)
    {
        foreach ($this->_elements as $index => $element) {
            if ($elementId == $element->getId()) {
                unset($this->_elements[$index]);
            }
        }
        // Renumber elements for further correct adding and removing other elements
        $this->_elements = array_merge($this->_elements, []);
        return $this;
    }

    /**
     * Count elements in collection
     */
    public function count(): int
    {
        return count($this->_elements);
    }

    /**
     * Find element by ID
     *
     * @param mixed $elementId
     * @return Varien_Data_Form_Element_Abstract|null
     */
    public function searchById($elementId)
    {
        foreach ($this->_elements as $element) {
            if ($element->getId() == $elementId) {
                return $element;
            }
        }
        return null;
    }
}
