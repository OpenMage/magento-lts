<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Db
 */

require_once 'Varien/Db/Tree/Node/Exception.php';

class Varien_Db_Tree_Node
{
    private $left;

    private $right;

    private $id;

    private $pid;

    private $level;

    private $data;

    public $hasChild = false;

    public $numChild = 0;

    /**
     * Varien_Db_Tree_Node constructor.
     * @param  array                         $nodeData
     * @param  array                         $keys
     * @throws Varien_Db_Tree_Node_Exception
     */
    public function __construct($nodeData, $keys)
    {
        if (empty($nodeData)) {
            throw new Varien_Db_Tree_Node_Exception('Empty array of node information');
        }

        if (empty($keys)) {
            throw new Varien_Db_Tree_Node_Exception('Empty keys array');
        }

        $this->id = $nodeData[$keys['id']];
        $this->pid = $nodeData[$keys['pid']];
        $this->left = $nodeData[$keys['left']];
        $this->right = $nodeData[$keys['right']];
        $this->level = $nodeData[$keys['level']];

        $this->data = $nodeData;
        $a = $this->right - $this->left;
        if ($a > 1) {
            $this->hasChild = true;
            $this->numChild = ($a - 1) / 2;
        }
    }

    public function getData($name)
    {
        return $this->data[$name] ?? null;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function getLeft()
    {
        return $this->left;
    }

    public function getRight()
    {
        return $this->right;
    }

    public function getPid()
    {
        return $this->pid;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Return true if node have chield
     *
     * @return bool
     */
    public function isParent()
    {
        if ($this->right - $this->left > 1) {
            return true;
        }

        return false;
    }
}
