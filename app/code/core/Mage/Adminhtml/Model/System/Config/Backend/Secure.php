<?php

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Secure extends Mage_Core_Model_Config_Data
{
    /**
     * Clean compiled JS/CSS when updating configuration settings
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            Mage::getModel('core/design_package')->cleanMergedJsCss();
        }
        return $this;
    }
}
