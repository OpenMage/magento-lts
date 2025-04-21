<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Data
 */

/**
 * Data tree
 *
 * @package    Varien_Data
 */
class Varien_Data_Tree
{
    /**
     * Nodes collection
     *
     * @var Varien_Data_Tree_Node_Collection
     */
    protected $_nodes;

    public function __construct()
    {
        $this->_nodes = new Varien_Data_Tree_Node_Collection($this);
    }

    /**
     * @return Varien_Data_Tree
     */
    public function getTree()
    {
        return $this;
    }

    /**
     * @param Varien_Data_Tree_Node $parentNode
     */
    public function load($parentNode = null) {}

    /**
     * @param int $nodeId
     */
    public function loadNode($nodeId) {}

    /**
     * @param array|Varien_Data_Tree_Node $data
     * @param Varien_Data_Tree_Node $parentNode
     * @param Varien_Data_Tree_Node $prevNode
     * @return Varien_Data_Tree_Node
     */
    public function appendChild($data, $parentNode, $prevNode = null)
    {
        if (is_array($data)) {
            $node = $this->addNode(
                new Varien_Data_Tree_Node($data, $parentNode->getIdField(), $this),
                $parentNode,
            );
        } elseif ($data instanceof Varien_Data_Tree_Node) {
            $node = $this->addNode($data, $parentNode);
        }
        return $node;
    }

    /**
     * @param Varien_Data_Tree_Node $node
     * @param Varien_Data_Tree_Node $parent
     * @return Varien_Data_Tree_Node
     */
    public function addNode($node, $parent = null)
    {
        $this->_nodes->add($node);
        $node->setParent($parent);
        if (!is_null($parent) && ($parent instanceof Varien_Data_Tree_Node)) {
            $parent->addChild($node);
        }
        return $node;
    }

    /**
     * @param Varien_Data_Tree_Node $node
     * @param Varien_Data_Tree_Node $parentNode
     * @param Varien_Data_Tree_Node $prevNode
     */
    public function moveNodeTo($node, $parentNode, $prevNode = null) {}

    /**
     * @param Varien_Data_Tree_Node $node
     * @param Varien_Data_Tree_Node $parentNode
     * @param Varien_Data_Tree_Node $prevNode
     */
    public function copyNodeTo($node, $parentNode, $prevNode = null) {}

    /**
     * @param Varien_Data_Tree_Node $node
     * @return Varien_Data_Tree
     */
    public function removeNode($node)
    {
        $this->_nodes->delete($node);
        if ($node->getParent()) {
            $node->getParent()->removeChild($node);
        }
        unset($node);
        return $this;
    }

    /**
     * @param Varien_Data_Tree_Node $parentNode
     * @param Varien_Data_Tree_Node $prevNode
     */
    public function createNode($parentNode, $prevNode = null) {}

    /**
     * @param Varien_Data_Tree_Node $node
     */
    public function getChild($node) {}

    /**
     * @param Varien_Data_Tree_Node $node
     */
    public function getChildren($node) {}

    /**
     * @return Varien_Data_Tree_Node_Collection
     */
    public function getNodes()
    {
        return $this->_nodes;
    }

    /**
     * @param int $nodeId
     * @return Varien_Data_Tree_Node
     */
    public function getNodeById($nodeId)
    {
        return $this->_nodes->searchById($nodeId);
    }

    /**
     * @param Varien_Data_Tree_Node $node
     * @return array
     */
    public function getPath($node)
    {
        if ($node instanceof Varien_Data_Tree_Node) {
        } elseif (is_numeric($node)) {
            if ($_node = $this->getNodeById($node)) {
                return $_node->getPath();
            }
        }
        return [];
    }
}
