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
 * @copyright  Copyright (c) 2021-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'Varien/Db/Tree/Node/Exception.php';

class Varien_Db_Tree_Node
{
    private $left;
    private $right;
    private $id;
    private $pid;
    private $level;
    private $title;
    private $data;

    public $hasChild = false;
    public $numChild = 0;

    /**
     * Varien_Db_Tree_Node constructor.
     * @param array $nodeData
     * @param array $keys
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
        return $this;
    }

    public function getData($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        } else {
            return null;
        }
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
     * @return boolean
     */
    public function isParent()
    {
        if ($this->right - $this->left > 1) {
            return true;
        } else {
            return false;
        }
    }
}
