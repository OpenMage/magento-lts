<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml backend model for "Use Custom Admin Path" option
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Admin_Usecustompath extends Mage_Core_Model_Config_Data
{
    /**
     * Check whether redirect should be set
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        if ($this->getOldValue() != $this->getValue()) {
            Mage::register('custom_admin_path_redirect', true, true);
        }

        return $this;
    }
}
