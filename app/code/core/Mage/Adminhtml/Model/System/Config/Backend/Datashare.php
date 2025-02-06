<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Config category field backend
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Datashare extends Mage_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        return $this;
    }
}
