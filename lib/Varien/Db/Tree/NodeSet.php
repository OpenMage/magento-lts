<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Db
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
