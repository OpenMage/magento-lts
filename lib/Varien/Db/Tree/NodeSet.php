<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Varien
 * @package    Varien_Db
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * TODO implements iterators
 */
class Varien_Db_Tree_NodeSet implements Iterator
{
    private $_nodes = [];
    private $_currentNode = 0;
    private $_current = 0;
    private $count = 0;

    public function __construct()
    {
        $this->_nodes = [];
        $this->_current = 0;
        $this->_currentNode = 0;
        $this->count = 0;
    }

    /**
     * @return int
     */
    public function addNode(Varien_Db_Tree_Node $node)
    {
        $this->_nodes[$this->_currentNode] = $node;
        $this->count++;
        return ++$this->_currentNode;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->count;
    }

    public function valid(): bool
    {
        return isset($this->_nodes[$this->_current]);
    }

    public function next(): void
    {
        if ($this->_current <= $this->_currentNode) {
            $this->_current++;
        }
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->_current;
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->_nodes[$this->_current];
    }

    public function rewind(): void
    {
        $this->_current = 0;
    }
}
