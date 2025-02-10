<?php
/**
 * Adminhtml backend model for "Use secret key in Urls" option
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Admin_Usesecretkey extends Mage_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        Mage::getSingleton('adminhtml/url')->renewSecretUrls();
        return $this;
    }
}
