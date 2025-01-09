<?php

/**
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Url rewrite suffix backend
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_System_Config_Backend_Catalog_Url_Rewrite_Suffix extends Mage_Core_Model_Config_Data
{
    /**
     * Check url rewrite suffix - whether we can support it
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        Mage::helper('core/url_rewrite')->validateSuffix($this->getValue());
        return $this;
    }
}
