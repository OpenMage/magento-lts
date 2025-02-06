<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml add store code to url backend
 *
 * @category   Mage
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
