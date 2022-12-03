<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Varien
 * @package    Varien_Db
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * TODO implements iterators
 *
 */
class Varien_Db_Tree_NodeSet implements Iterator
{
    private $_nodes = [];
    private $_currentNode = 0;
    private $_current = 0;

    public function __construct()
    {
        $this->_nodes = [];
        $this->_current = 0;
        $this->_currentNode = 0;
        $this->count = 0;
    }

    public function addNode(Varien_Db_Tree_Node $node)
    {
        $this->_nodes[$this->_currentNode] = $node;
        $this->count++;
        return ++$this->_currentNode;
    }

    public function count()
    {
        return $this->count;
    }

    public function valid()
    {
        return  isset($this->_nodes[$this->_current]);
    }

    public function next()
    {
        if ($this->_current > $this->_currentNode) {
            return false;
        } else {
            return  $this->_current++;
        }
    }

    public function key()
    {
        return $this->_current;
    }

    public function current()
    {
        return $this->_nodes[$this->_current];
    }

    public function rewind()
    {
        $this->_current = 0;
    }
}
