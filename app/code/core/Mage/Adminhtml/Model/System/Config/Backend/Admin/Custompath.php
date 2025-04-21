<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Adminhtml backend model for "Custom Admin Path" option
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Admin_Custompath extends Mage_Core_Model_Config_Data
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
