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
 * Tree node collection
 *
 * @category   Varien
 * @package    Varien_Data
 */
class Varien_Data_Tree_Node_Collection implements ArrayAccess, IteratorAggregate, Countable
{
    /**
     * @var Varien_Data_Tree_Node[]
     */
    private $_nodes;

    /**
     * @var Varien_Data_Tree
     */
    private $_container;

    /**
     * Varien_Data_Tree_Node_Collection constructor.
     * @param $container
     */
    public function __construct($container)
    {
        $this->_nodes = [];
        $this->_container = $container;
    }

    /**
     * @return Varien_Data_Tree_Node[]
     */
    public function getNodes()
    {
        return $this->_nodes;
    }

    /**
    * Implementation of IteratorAggregate::getIterator()
    */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->_nodes);
    }

    /**
     * Implementation of ArrayAccess:offsetSet()
     * @param string $key
     * @param string $value
     */
    public function offsetSet($key, $value): void
    {
        $this->_nodes[$key] = $value;
    }

    /**
     * Implementation of ArrayAccess:offsetGet()
     * @param string $key
     * @return mixed|Varien_Data_Tree_Node
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($key)
    {
        return $this->_nodes[$key];
    }

    /**
     * Implementation of ArrayAccess:offsetUnset()
     * @param string $key
     */
    public function offsetUnset($key): void
    {
        unset($this->_nodes[$key]);
    }

    /**
     * Implementation of ArrayAccess:offsetExists()
     * @param string $key
     */
    public function offsetExists($key): bool
    {
        return isset($this->_nodes[$key]);
    }

    /**
     * Adds a node to this node
     * @return Varien_Data_Tree_Node
     */
    public function add(Varien_Data_Tree_Node $node)
    {
        $node->setParent($this->_container);

        // Set the Tree for the node
        if ($this->_container->getTree() instanceof Varien_Data_Tree) {
            $node->setTree($this->_container->getTree());
        }

        $this->_nodes[$node->getId()] = $node;

        return $node;
    }

    /**
     * @param Varien_Data_Tree_Node $node
     * @return $this
     */
    public function delete($node)
    {
        $id = $node->getId();
        if (isset($this->_nodes[$id])) {
            unset($this->_nodes[$id]);
        }
        return $this;
    }

    /**
     * Implementation of Countable:count()
     */
    public function count(): int
    {
        return count($this->_nodes);
    }

    /**
     * @return Varien_Data_Tree_Node|null
     */
    public function lastNode()
    {
        return !empty($this->_nodes) ? $this->_nodes[count($this->_nodes) - 1] : null;
    }

    /**
     * @param $nodeId
     * @return Varien_Data_Tree_Node|null
     */
    public function searchById($nodeId)
    {
        return $this->_nodes[$nodeId] ?? null;
    }
}
