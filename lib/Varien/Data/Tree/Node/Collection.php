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
 * @package     Varien_Data
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tree node collection
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
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
        $this->_nodes = array();
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
    public function getIterator()
    {
        return new ArrayIterator($this->_nodes);
    }

    /**
     * Implementation of ArrayAccess:offsetSet()
     * @param string $key
     * @param string $value
     */
    public function offsetSet($key, $value)
    {
        $this->_nodes[$key] = $value;
    }

    /**
     * Implementation of ArrayAccess:offsetGet()
     * @param string $key
     * @return Varien_Data_Tree_Node
     */
    public function offsetGet($key)
    {
        return $this->_nodes[$key];
    }

    /**
     * Implementation of ArrayAccess:offsetUnset()
     * @param string $key
     */
    public function offsetUnset($key)
    {
        unset($this->_nodes[$key]);
    }

    /**
     * Implementation of ArrayAccess:offsetExists()
     * @param string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return isset($this->_nodes[$key]);
    }

    /**
     * Adds a node to this node
     * @param Varien_Data_Tree_Node $node
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
        if (isset($this->_nodes[$node->getId()])) {
            unset($this->_nodes[$node->getId()]);
        }
        return $this;
    }

    /**
     * Implementation of Countable:count()
     *
     * @return int
     */
    public function count()
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
        if (isset($this->_nodes[$nodeId])) {
            return $this->_nodes[$nodeId];
        }
        return null;
    }
}
