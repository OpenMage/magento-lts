<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Data
 */

/**
 * Data DB tree
 *
 * Data model:
 * id  |  pid  |  level | order
 *
 * @package    Varien_Data
 */
class Varien_Data_Tree_Db extends Varien_Data_Tree
{
    public const ID_FIELD      = 'id';

    public const PARENT_FIELD  = 'parent';

    public const LEVEL_FIELD   = 'level';

    public const ORDER_FIELD   = 'order';

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

    protected $_parentField;

    protected $_levelField;

    protected $_orderField;

    /**
     * Db tree constructor
     *
     * $fields = array(
     *      Varien_Data_Tree_Db::ID_FIELD       => string,
     *      Varien_Data_Tree_Db::PARENT_FIELD   => string,
     *      Varien_Data_Tree_Db::LEVEL_FIELD    => string
     *      Varien_Data_Tree_Db::ORDER_FIELD    => string
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
            throw new Exception('Wrong "$connection" parameter');
        }

        $this->_conn    = $connection;
        $this->_table   = $table;

        if (!isset($fields[self::ID_FIELD])
            || !isset($fields[self::PARENT_FIELD])
            || !isset($fields[self::LEVEL_FIELD])
            || !isset($fields[self::ORDER_FIELD])
        ) {
            throw new Exception('"$fields" tree configuratin array');
        }

        $this->_idField     = $fields[self::ID_FIELD];
        $this->_parentField = $fields[self::PARENT_FIELD];
        $this->_levelField  = $fields[self::LEVEL_FIELD];
        $this->_orderField  = $fields[self::ORDER_FIELD];

        $this->_select  = $this->_conn->select();
        $this->_select->from($this->_table, array_values($fields));
    }

    /**
     * @return Zend_Db_Select
     */
    public function getDbSelect()
    {
        return $this->_select;
    }

    /**
     * @param Zend_Db_Select $select
     */
    public function setDbSelect($select)
    {
        $this->_select = $select;
    }

    /**
     * Load tree
     *
     * @param   int|Varien_Data_Tree_Node $parentNode
     * @param   int $recursionLevel recursion level
     * @return  $this
     */
    public function load($parentNode = null, $recursionLevel = 100)
    {
        if (is_null($parentNode)) {
            $this->_loadFullTree();
            return $this;
        } elseif ($parentNode instanceof Varien_Data_Tree_Node) {
            $parentId = $parentNode->getId();
        } elseif (is_numeric($parentNode)) {
            $parentId = $parentNode;
            $parentNode = null;
        } else {
            throw new Exception('root node id is not defined');
        }

        $select = clone $this->_select;
        $select->order($this->_table . '.' . $this->_orderField . ' ASC');

        $condition = $this->_conn->quoteInto("$this->_table.$this->_parentField=?", $parentId);
        $select->where($condition);
        $arrNodes = $this->_conn->fetchAll($select);
        foreach ($arrNodes as $nodeInfo) {
            $node = new Varien_Data_Tree_Node($nodeInfo, $this->_idField, $this, $parentNode);
            $this->addNode($node, $parentNode);

            if ($recursionLevel) {
                $node->loadChildren($recursionLevel - 1);
            }
        }

        return $this;
    }

    /**
     * @param int $nodeId
     * @return Varien_Data_Tree_Node
     */
    public function loadNode($nodeId)
    {
        $select = clone $this->_select;
        $condition = $this->_conn->quoteInto("$this->_table.$this->_idField=?", $nodeId);
        $select->where($condition);
        $node = new Varien_Data_Tree_Node($this->_conn->fetchRow($select), $this->_idField, $this);
        $this->addNode($node);
        return $node;
    }

    /**
     * @param array $data
     * @param Varien_Data_Tree_Node $parentNode
     * @param Varien_Data_Tree_Node|null $prevNode
     * @return Varien_Data_Tree_Node
     * @throws Zend_Db_Adapter_Exception
     */
    public function appendChild($data, $parentNode, $prevNode = null)
    {
        $orderSelect = $this->_conn->select();
        $orderSelect->from($this->_table, new Zend_Db_Expr('MAX(' . $this->_conn->quoteIdentifier($this->_orderField) . ')'))
            ->where($this->_conn->quoteIdentifier($this->_parentField) . '=' . $parentNode->getId());

        $order = $this->_conn->fetchOne($orderSelect);
        $data[$this->_parentField] = $parentNode->getId();
        $data[$this->_levelField] = $parentNode->getData($this->_levelField) + 1;
        $data[$this->_orderField] = $order + 1;

        $this->_conn->insert($this->_table, $data);
        $data[$this->_idField] = $this->_conn->lastInsertId();

        return parent::appendChild($data, $parentNode, $prevNode);
    }

    /**
     * Move tree node
     *
     * @param Varien_Data_Tree_Node $node
     * @param Varien_Data_Tree_Node $parentNode
     * @param Varien_Data_Tree_Node $prevNode
     * @throws Exception
     */
    public function moveNodeTo($node, $parentNode, $prevNode = null)
    {
        $data = [];
        $data[$this->_parentField]  = $parentNode->getId();
        $data[$this->_levelField]   = $parentNode->getData($this->_levelField) + 1;
        // New node order
        if (is_null($prevNode) || is_null($prevNode->getData($this->_orderField))) {
            $data[$this->_orderField] = 1;
        } else {
            $data[$this->_orderField] = $prevNode->getData($this->_orderField) + 1;
        }

        $condition = $this->_conn->quoteInto("$this->_idField=?", $node->getId());

        // For reorder new node branch
        $dataReorderNew = [
            $this->_orderField => new Zend_Db_Expr($this->_conn->quoteIdentifier($this->_orderField) . '+1'),
        ];
        $conditionReorderNew = $this->_conn->quoteIdentifier($this->_parentField) . '=' . $parentNode->getId() .
                            ' AND ' . $this->_conn->quoteIdentifier($this->_orderField) . '>=' . $data[$this->_orderField];

        // For reorder old node branch
        $dataReorderOld = [
            $this->_orderField => new Zend_Db_Expr($this->_conn->quoteIdentifier($this->_orderField) . '-1'),
        ];
        $conditionReorderOld = $this->_conn->quoteIdentifier($this->_parentField) . '=' . $node->getData($this->_parentField) .
                            ' AND ' . $this->_conn->quoteIdentifier($this->_orderField) . '>' . $node->getData($this->_orderField);

        $this->_conn->beginTransaction();
        try {
            // Prepare new node branch
            $this->_conn->update($this->_table, $dataReorderNew, $conditionReorderNew);
            // Move node
            $this->_conn->update($this->_table, $data, $condition);
            // Update old node branch
            $this->_conn->update($this->_table, $dataReorderOld, $conditionReorderOld);
            $this->_updateChildLevels($node->getId(), $data[$this->_levelField]);
            $this->_conn->commit();
        } catch (Exception $exception) {
            $this->_conn->rollBack();
            throw new Exception('Can\'t move tree node', $exception->getCode(), $exception);
        }
    }

    /**
     * @param int $parentId
     * @param int $parentLevel
     * @return $this
     * @throws Zend_Db_Adapter_Exception
     */
    protected function _updateChildLevels($parentId, $parentLevel)
    {
        $select = $this->_conn->select()
            ->from($this->_table, $this->_idField)
            ->where($this->_parentField . '=?', $parentId);
        $ids = $this->_conn->fetchCol($select);

        if (!empty($ids)) {
            $this->_conn->update(
                $this->_table,
                [$this->_levelField => $parentLevel + 1],
                $this->_conn->quoteInto($this->_idField . ' IN (?)', $ids),
            );
            foreach ($ids as $id) {
                $this->_updateChildLevels($id, $parentLevel + 1);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function _loadFullTree()
    {
        $select = clone $this->_select;
        $select->order($this->_table . '.' . $this->_levelField)
            ->order($this->_table . '.' . $this->_orderField);

        $arrNodes = $this->_conn->fetchAll($select);

        foreach ($arrNodes as $nodeInfo) {
            $node = new Varien_Data_Tree_Node($nodeInfo, $this->_idField, $this);
            $parentNode = $this->getNodeById($nodeInfo[$this->_parentField]);
            $this->addNode($node, $parentNode);
        }

        return $this;
    }

    /**
     * @param Varien_Data_Tree_Node $node
     * @return $this|Varien_Data_Tree
     * @throws Exception
     */
    public function removeNode($node)
    {
        // For reorder old node branch
        $dataReorderOld = [
            $this->_orderField => new Zend_Db_Expr($this->_conn->quoteIdentifier($this->_orderField) . '-1'),
        ];
        $conditionReorderOld = $this->_conn->quoteIdentifier($this->_parentField) . '=' . $node->getData($this->_parentField) .
                            ' AND ' . $this->_conn->quoteIdentifier($this->_orderField) . '>' . $node->getData($this->_orderField);

        $this->_conn->beginTransaction();
        try {
            $condition = $this->_conn->quoteInto("$this->_idField=?", $node->getId());
            $this->_conn->delete($this->_table, $condition);
            // Update old node branch
            $this->_conn->update($this->_table, $dataReorderOld, $conditionReorderOld);
            $this->_conn->commit();
        } catch (Exception $exception) {
            $this->_conn->rollBack();
            throw new Exception('Can\'t remove tree node', $exception->getCode(), $exception);
        }

        parent::removeNode($node);
        return $this;
    }
}
