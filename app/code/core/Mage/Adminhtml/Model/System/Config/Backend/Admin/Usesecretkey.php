<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml backend model for "Use secret key in Urls" option
 *
 * @category   Mage
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
