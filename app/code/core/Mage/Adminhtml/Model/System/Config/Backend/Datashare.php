<?php
/**
 * Config category field backend
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Datashare extends Mage_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        return $this;
    }
}
