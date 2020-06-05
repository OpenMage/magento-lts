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
 * Data DB tree
 *
 * Data model:
 * id  |  path  |  order
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Tree_Dbp extends Varien_Data_Tree
{

    const ID_FIELD      = 'id';
    const PATH_FIELD    = 'path';
    const ORDER_FIELD   = 'order';
    const LEVEL_FIELD   = 'level';

    /**
     * DB connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_conn;

    /**
     * Data table name
     *
     * @var string
     */
    protected $_table;

    protected $_loaded = false;

    /**
     * SQL select object
     *
     * @var Zend_Db_Select
     */
    protected $_select;

    /**
     * Tree ctructure field names
     *
     * @var string
     */
    protected $_idField;
    protected $_pathField;
    protected $_orderField;
    protected $_levelField;

    /**
     * Db tree constructor
     *
     * $fields = array(
     *      Varien_Data_Tree_Dbp::ID_FIELD       => string,
     *      Varien_Data_Tree_Dbp::PATH_FIELD     => string,
     *      Varien_Data_Tree_Dbp::ORDER_FIELD    => string
     *      Varien_Data_Tree_Dbp::LEVEL_FIELD    => string
     * )
     *
     * @param Zend_Db_Adapter_Abstract $connection
     * @param string $table
     * @param array $fields
     */
    public function __construct($connection, $table, $fields)
    {
        parent::__construct();

        if (!$connection) {
            throw new Exception('Wrong "$connection" parametr');
        }

        $this->_conn    = $connection;
        $this->_table   = $table;

        if (!isset($fields[self::ID_FIELD]) ||
            !isset($fields[self::PATH_FIELD]) ||
            !isset($fields[self::LEVEL_FIELD]) ||
            !isset($fields[self::ORDER_FIELD])) {

            throw new Exception('"$fields" tree configuratin array');
        }

        $this->_idField     = $fields[self::ID_FIELD];
        $this->_pathField   = $fields[self::PATH_FIELD];
        $this->_orderField  = $fields[self::ORDER_FIELD];
        $this->_levelField  = $fields[self::LEVEL_FIELD];

        $this->_select  = $this->_conn->select();
        $this->_select->from($this->_table);
    }

    /**
     * Retrieve current select object
     *
     * @return Varien_Db_Select
     */
    public function getDbSelect()
    {
        return $this->_select;
    }

    /**
     * Set Select object
     *
     * @param Varien_Db_Select $select
     */
    public function setDbSelect($select)
    {
        $this->_select = $select;
    }

    /**
     * Load tree
     *
     * @param   int|Varien_Data_Tree_Node $parentNode
     * @return  Varien_Data_Tree_Dbp
     */
    public function load($parentNode=null, $recursionLevel = 0)
    {
        if (!$this->_loaded) {
            $startLevel = 1;
            $parentPath = '';

            if ($parentNode instanceof Varien_Data_Tree_Node) {
                $parentPath = $parentNode->getData($this->_pathField);
                $startLevel = $parentNode->getData($this->_levelField);
            } else if (is_numeric($parentNode)) {
                $select = $this->_conn->select()
                    ->from($this->_table, array($this->_pathField, $this->_levelField))
                    ->where("{$this->_idField} = ?", $parentNode);
                $parent = $this->_conn->fetchRow($select);

                $startLevel = $parent[$this->_levelField];
                $parentPath = $parent[$this->_pathField];
                $parentNode = null;
            } else if (is_string($parentNode)) {
                $parentPath = $parentNode;
                $startLevel = count(explode($parentPath))-1;
                $parentNode = null;
            }

            $select = clone $this->_select;

            $select->order($this->_table . '.' . $this->_orderField . ' ASC');
            if ($parentPath) {
                $pathField = $this->_conn->quoteIdentifier(array($this->_table, $this->_pathField));
                $select->where("{$pathField} LIKE ?", "{$parentPath}/%");
            }
            if ($recursionLevel != 0) {
                $levelField = $this->_conn->quoteIdentifier(array($this->_table, $this->_levelField));
                $select->where("{$levelField} <= ?", $startLevel + $recursionLevel);
            }

            $arrNodes = $this->_conn->fetchAll($select);

            $childrenItems = array();

            foreach ($arrNodes as $nodeInfo) {
                $pathToParent = explode('/', $nodeInfo[$this->_pathField]);
                array_pop($pathToParent);
                $pathToParent = implode('/', $pathToParent);
                $childrenItems[$pathToParent][] = $nodeInfo;
            }

            $this->addChildNodes($childrenItems, $parentPath, $parentNode);

            $this->_loaded = true;
        }

        return $this;
    }

    public function addChildNodes($children, $path, $parentNode, $level = 0)
    {
        if (isset($children[$path])) {
            foreach ($children[$path] as $child) {
                $nodeId = isset($child[$this->_idField])?$child[$this->_idField]:false;
                if ($parentNode && $nodeId && $node = $parentNode->getChildren()->searchById($nodeId)) {
                    $node->addData($child);
                } else {
                    $node = new Varien_Data_Tree_Node($child, $this->_idField, $this, $parentNode);
                }

                //$node->setLevel(count(explode('/', $node->getData($this->_pathField)))-1);
                $node->setLevel($node->getData($this->_levelField));
                $node->setPathId($node->getData($this->_pathField));
                $this->addNode($node, $parentNode);


                if ($path) {
                    $childrenPath = explode('/', $path);
                } else {
                    $childrenPath = array();
                }
                $childrenPath[] = $node->getId();
                $childrenPath = implode('/', $childrenPath);

                $this->addChildNodes($children, $childrenPath, $node, $level+1);
            }
        }
    }

    /**
     * Enter description here...
     *
     * @param int|string $nodeId
     * @return Varien_Data_Tree_Node
     */
    public function loadNode($nodeId)
    {
        $select = clone $this->_select;
        if (is_numeric($nodeId)) {
            $condField = $this->_conn->quoteIdentifier(array($this->_table, $this->_idField));
        } else {
            $condField = $this->_conn->quoteIdentifier(array($this->_table, $this->_pathField));
        }

        $select->where("{$condField} = ?", $nodeId);

        $node = new Varien_Data_Tree_Node($this->_conn->fetchRow($select), $this->_idField, $this);
        $this->addNode($node);
        return $node;
    }

    public function getChildren($node, $recursive = true, $result = array()) {
        if (is_numeric($node)) {
            $node = $this->getNodeById($node);
        }
        if (!$node) {
            return $result;
        }

        foreach ($node->getChildren() as $child) {
            if ($recursive) {
                if ($child->getChildren()) {
                    $result = $this->getChildren($child, $recursive, $result);
                }
            }
            $result[] = $child->getId();
        }
        return $result;
    }

    /**
     * Move tree node
     *
     * @todo Use adapter for generate conditions
     * @param Varien_Data_Tree_Node $node
     * @param Varien_Data_Tree_Node $newParent
     * @param Varien_Data_Tree_Node $prevNode
     */
    public function move($node, $newParent, $prevNode = null)
    {
        $position = 1;

        $oldPath = $node->getData($this->_pathField);
        $newPath = $newParent->getData($this->_pathField);

        $newPath = $newPath . '/' . $node->getId();
        $oldPathLength = strlen($oldPath);

        $newLevel = $newParent->getLevel()+1;
        $levelDisposition = $newLevel-$node->getLevel();

        $data = array(
            $this->_levelField => new Zend_Db_Expr("{$this->_levelField} + '{$levelDisposition}'"),
            $this->_pathField  => new Zend_Db_Expr("CONCAT('$newPath', RIGHT($this->_pathField, LENGTH($this->_pathField) - {$oldPathLength}))")
        );
        $condition = $this->_conn->quoteInto("$this->_pathField REGEXP ?", "^$oldPath(/|$)");

        $this->_conn->beginTransaction();

        $reorderData = array($this->_orderField => new Zend_Db_Expr("$this->_orderField + 1"));
        try {
            if ($prevNode && $prevNode->getId()) {
                $reorderCondition = "{$this->_orderField} > {$prevNode->getData($this->_orderField)}";
                $position = $prevNode->getData($this->_orderField) + 1;
            } else {
                $reorderCondition = $this->_conn->quoteInto("{$this->_pathField} REGEXP ?", "^{$newParent->getData($this->_pathField)}/[0-9]+$");
                $select = $this->_conn->select()
                    ->from($this->_table, new Zend_Db_Expr("MIN({$this->_orderField})"))
                    ->where($reorderCondition);

                $position = (int) $this->_conn->fetchOne($select);
            }
            $this->_conn->update($this->_table, $reorderData, $reorderCondition);
            $this->_conn->update($this->_table, $data, $condition);
            $this->_conn->update($this->_table, array($this->_orderField => $position, $this->_levelField=>$newLevel),
                $this->_conn->quoteInto("{$this->_idField} = ?", $node->getId())
            );

            $this->_conn->commit();
        } catch (Exception $e){
            $this->_conn->rollBack();
            throw new Exception("Can't move tree node due to error: " . $e->getMessage());
        }
    }

    public function loadEnsuredNodes($category, $rootNode)
    {
        $pathIds = $category->getPathIds();
        $rootNodeId = $rootNode->getId();
        $rootNodePath = $rootNode->getData($this->_pathField);

        $select = clone $this->_select;
        $select->order($this->_table.'.'.$this->_orderField . ' ASC');

        if ($pathIds) {
            $condition = $this->_conn->quoteInto("$this->_table.$this->_idField in (?)", $pathIds);
            $select->where($condition);
        }

        $arrNodes = $this->_conn->fetchAll($select);

        if ($arrNodes) {
            $childrenItems = array();
            foreach ($arrNodes as $nodeInfo) {
                $nodeId = $nodeInfo[$this->_idField];
                if ($nodeId<=$rootNodeId) {
                    continue;
                }

                $pathToParent = explode('/', $nodeInfo[$this->_pathField]);
                array_pop($pathToParent);
                $pathToParent = implode('/', $pathToParent);
                $childrenItems[$pathToParent][] = $nodeInfo;
            }

            $this->_addChildNodes($childrenItems, $rootNodePath, $rootNode, true);
        }
    }

    protected function _addChildNodes($children, $path, $parentNode, $withChildren=false, $level = 0)
    {
        if (isset($children[$path])) {
            foreach ($children[$path] as $child) {
                $nodeId = isset($child[$this->_idField])?$child[$this->_idField]:false;
                if ($parentNode && $nodeId && $node = $parentNode->getChildren()->searchById($nodeId)) {
                    $node->addData($child);
                } else {
                    $node = new Varien_Data_Tree_Node($child, $this->_idField, $this, $parentNode);
                    $node->setLevel($node->getData($this->_levelField));
                    $node->setPathId($node->getData($this->_pathField));
                    $this->addNode($node, $parentNode);
                }

                if ($withChildren) {
                    $this->_loaded = false;
                    $node->loadChildren(1);
                    $this->_loaded = false;
                }

                if ($path) {
                    $childrenPath = explode('/', $path);
                } else {
                    $childrenPath = array();
                }
                $childrenPath[] = $node->getId();
                $childrenPath = implode('/', $childrenPath);

                $this->_addChildNodes($children, $childrenPath, $node, $withChildren, $level+1);
            }
        }
    }
}
