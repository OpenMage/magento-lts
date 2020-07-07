<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Varien
 * @package     Varien_Db
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * TODO implements iterators
 *
 */
class Varien_Db_Tree_NodeSet implements Iterator
{
    private $_nodes = array();
    private $_currentNode = 0;
    private $_current = 0;


    function __construct() {
        $this->_nodes = array();
        $this->_current = 0;
        $this->_currentNode = 0;
        $this->count = 0;
    }



    function addNode(Varien_Db_Tree_Node $node) {
        $this->_nodes[$this->_currentNode] = $node;
        $this->count++;
        return ++$this->_currentNode;
    }

    function count() {
        return $this->count;
    }


    function valid() {
        return  isset($this->_nodes[$this->_current]);
    }

    function next() {
        if ($this->_current > $this->_currentNode) {
            return false;
        } else {
            return  $this->_current++;
        }
    }

    function key() {
        return $this->_current;
    }


    function current() {
        return $this->_nodes[$this->_current];
    }

    function rewind() {
        $this->_current = 0;
    }
}
