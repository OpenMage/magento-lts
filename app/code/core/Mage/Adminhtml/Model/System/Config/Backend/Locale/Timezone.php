<?php
/**
 * System config email field backend model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Locale_Timezone extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        $allWithBc = DateTimeZone::ALL_WITH_BC;
        if (!in_array($this->getValue(), DateTimeZone::listIdentifiers($allWithBc))) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid timezone'));
        }

        return $this;
    }
}
