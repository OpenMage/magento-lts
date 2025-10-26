<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_Report_Item extends Varien_Object
{
    protected $_isEmpty  = false;

    protected $_children = [];

    public function setIsEmpty($flag = true)
    {
        $this->_isEmpty = $flag;
        return $this;
    }

    public function getIsEmpty()
    {
        return $this->_isEmpty;
    }

    public function hasIsEmpty() {}

    public function getChildren()
    {
        return $this->_children;
    }

    public function setChildren($children)
    {
        $this->_children = $children;
        return $this;
    }

    public function hasChildren()
    {
        return count($this->_children) > 0;
    }

    public function addChild($child)
    {
        $this->_children[] = $child;
        return $this;
    }
}
