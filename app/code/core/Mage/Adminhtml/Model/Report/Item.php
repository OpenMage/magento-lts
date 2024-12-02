<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
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

    public function hasIsEmpty()
    {
    }

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
