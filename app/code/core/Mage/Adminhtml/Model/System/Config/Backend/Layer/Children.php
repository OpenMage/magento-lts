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
class Mage_Adminhtml_Model_System_Config_Backend_Layer_Children extends Mage_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        Mage::getSingleton('catalogindex/indexer')->plainReindex();
        return $this;
    }
}
