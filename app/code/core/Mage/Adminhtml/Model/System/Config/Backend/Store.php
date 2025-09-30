<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml add store code to url backend
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Store extends Mage_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        Mage::app()->getStore()->setConfig(Mage_Core_Model_Store::XML_PATH_STORE_IN_URL, $this->getValue());
        Mage::app()->cleanCache();
        return $this;
    }
}
