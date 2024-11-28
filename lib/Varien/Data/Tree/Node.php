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
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Data tree node
 *
 * @method int getLevel()
 * @method string getClass()
 * @method string getPositionClass()
 * @method string getOutermostClass()
 * @method $this setOutermostClass(string $class)
 * @method $this setChildrenWrapClass(string $class)
 * @method bool getIsFirst()
 * @method bool getIsLast()
 *
 * @category   Varien
 * @package    Varien_Data
 */
class Varien_Data_Tree_Node extends Varien_Object
{
    /**
     * Parent node
     *
     * @var Varien_Data_Tree_Node
     */
    protected $_parent;

    /**
     * Main tree object
     *
     * @var Varien_Data_Tree
     */
    protected $_tree;

    /**
     * Child nodes
     *
     * @var Varien_Data_Tree_Node_Collection
     */
    protected $_childNodes;

    /**
     * Node ID field name
     *
     * @var string
     */
    protected $_idField;

    /**
     * Data tree node constructor
     *
     * @param array $data
     * @param string $idFeild
     * @param Varien_Data_Tree $tree
     * @param Varien_Data_Tree_Node $parent
     */
    public function __construct($data, $idFeild, $tree, $parent = null)
    {
        $this->setTree($tree);
        $this->setParent($parent);
        $this->setIdField($idFeild);
        $this->setData($data);
        $this->_childNodes = new Varien_Data_Tree_Node_Collection($this);
    }

    /**
     * Retrieve node id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->getData($this->getIdField());
    }

    /**
     * Set node id field name
     *
     * @param   string $idField
     * @return  $this
     */
    public function setIdField($idField)
    {
        $this->_idField = $idField;
        return $this;
    }

    /**
     * Retrieve node id field name
     *
     * @return string
     */
    public function getIdField()
    {
        return $this->_idField;
    }

    /**
     * Set node tree object
     *
     * @return  $this
     */
    public function setTree(Varien_Data_Tree $tree)
    {
        $this->_tree = $tree;
        return $this;
    }

    /**
     * Retrieve node tree object
     *
     * @return Varien_Data_Tree
     */
    public function getTree()
    {
        return $this->_tree;
    }

    /**
     * Set node parent
     *
     * @param   Varien_Data_Tree_Node $parent
     * @return  Varien_Data_Tree_Node
     */
    public function setParent($parent)
    {
        $this->_parent = $parent;
        return $this;
    }

    /**
     * Retrieve node parent
     *
     * @return Varien_Data_Tree_Node
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Check node children
     *
     * @return bool
     */
    public function hasChildren()
    {
        return $this->_childNodes->count() > 0;
    }

    /**
     * @param int $level
     * @return $this
     */
    public function setLevel($level)
    {
        $this->setData('level', $level);
        return $this;
    }

    /**
     * @param int $path
     * @return $this
     */
    public function setPathId($path)
    {
        $this->setData('path_id', $path);
        return $this;
    }

    /**
     * @param Varien_Data_Tree_Node $node
     * @todo LTS implement
     */
    public function isChildOf($node)
    {
    }

    /**
     * Load node children
     *
     * @param   int  $recursionLevel
     * @return  Varien_Data_Tree_Node
     */
    public function loadChildren($recursionLevel = 0)
    {
        $this->_tree->load($this, $recursionLevel);
        return $this;
    }

    /**
     * Retrieve node children collection
     *
     * @return Varien_Data_Tree_Node_Collection
     */
    public function getChildren()
    {
        return $this->_childNodes;
    }

    /**
     * @param array $nodes
     * @return Varien_Data_Tree_Node[]
     */
    public function getAllChildNodes(&$nodes = [])
    {
        foreach ($this->_childNodes as $node) {
            $nodes[$node->getId()] = $node;
            $node->getAllChildNodes($nodes);
        }
        return $nodes;
    }

    /**
     * @return Varien_Data_Tree_Node
     */
    public function getLastChild()
    {
        return $this->_childNodes->lastNode();
    }

    /**
     * Add child node
     *
     * @param   Varien_Data_Tree_Node $node
     * @return  Varien_Data_Tree_Node
     */
    public function addChild($node)
    {
        $this->_childNodes->add($node);
        return $this;
    }

    /**
     * @param Varien_Data_Tree_Node|null $prevNode
     * @return $this
     */
    public function appendChild($prevNode = null)
    {
        $this->_tree->appendChild($this, $prevNode);
        return $this;
    }

    /**
     * @param Varien_Data_Tree_Node $parentNode
     * @param Varien_Data_Tree_Node|null $prevNode
     * @return $this
     */
    public function moveTo($parentNode, $prevNode = null)
    {
        $this->_tree->moveNodeTo($this, $parentNode, $prevNode);
        return $this;
    }

    /**
     * @param Varien_Data_Tree_Node $parentNode
     * @param Varien_Data_Tree_Node|null $prevNode
     * @return $this
     */
    public function copyTo($parentNode, $prevNode = null)
    {
        $this->_tree->copyNodeTo($this, $parentNode, $prevNode);
        return $this;
    }

    /**
     * @param Varien_Data_Tree_Node $childNode
     * @return $this
     */
    public function removeChild($childNode)
    {
        $this->_childNodes->delete($childNode);
        return $this;
    }

    /**
     * @param array $prevNodes
     * @return array
     */
    public function getPath(&$prevNodes = [])
    {
        if ($this->_parent) {
            $prevNodes[] = $this;
            $this->_parent->getPath($prevNodes);
        }
        return $prevNodes;
    }

    /**
     * @return bool
     */
    public function getIsActive()
    {
        return $this->_getData('is_active');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_getData('name');
    }
}
