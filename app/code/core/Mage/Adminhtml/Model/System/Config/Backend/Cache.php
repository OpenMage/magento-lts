<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Cache cleaner backend model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Cache extends Mage_Core_Model_Config_Data
{
    /**
     * Cache tags to clean
     *
     * @var array
     */
    protected $_cacheTags = [];

    /**
     * Clean cache, value was changed
     *
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            Mage::app()->cleanCache($this->_cacheTags);
        }
        return $this;
    }
}
