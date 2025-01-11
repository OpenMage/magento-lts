<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml backend model for "Custom Admin URL" option
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Admin_Custom extends Mage_Core_Model_Config_Data
{
    public const CONFIG_SCOPE                      = 'stores';
    public const CONFIG_SCOPE_ID                   = 0;

    public const XML_PATH_UNSECURE_BASE_URL        = 'web/unsecure/base_url';
    public const XML_PATH_SECURE_BASE_URL          = 'web/secure/base_url';
    public const XML_PATH_UNSECURE_BASE_LINK_URL   = 'web/unsecure/base_link_url';
    public const XML_PATH_SECURE_BASE_LINK_URL     = 'web/secure/base_link_url';

    /**
     * Validate value before save
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();

        if (!empty($value) && substr($value, -2) !== '}}') {
            $value = rtrim($value, '/') . '/';
        }

        $this->setValue($value);
        return $this;
    }

    /**
     * Change secure/unsecure base_url after use_custom_url was modified
     *
     * @return $this
     */
    public function _afterSave()
    {
        $useCustomUrl = $this->getData('groups/url/fields/use_custom/value');
        $value = $this->getValue();

        if ($useCustomUrl == 1 && empty($value)) {
            return $this;
        }

        if ($useCustomUrl == 1) {
            Mage::getConfig()->saveConfig(
                self::XML_PATH_SECURE_BASE_URL,
                $value,
                self::CONFIG_SCOPE,
                self::CONFIG_SCOPE_ID,
            );
            Mage::getConfig()->saveConfig(
                self::XML_PATH_UNSECURE_BASE_URL,
                $value,
                self::CONFIG_SCOPE,
                self::CONFIG_SCOPE_ID,
            );
        }

        return $this;
    }
}
