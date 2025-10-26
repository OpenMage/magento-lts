<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml backend model for "Use Custom Admin URL" option
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Admin_Usecustom extends Mage_Core_Model_Config_Data
{
    /**
     * Validate custom url
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if ($value == 1) {
            $customUrl = $this->getData('groups/url/fields/custom/value');
            if (empty($customUrl)) {
                Mage::throwException(Mage::helper('adminhtml')->__('Please specify the admin custom URL.'));
            }
        }

        return $this;
    }

    /**
     * Delete custom admin url from configuration if "Use Custom Admin Url" option disabled
     *
     * @return $this
     */
    protected function _afterSave()
    {
        $value = $this->getValue();

        if (!$value) {
            Mage::getConfig()->deleteConfig(
                Mage_Adminhtml_Model_System_Config_Backend_Admin_Custom::XML_PATH_SECURE_BASE_URL,
                Mage_Adminhtml_Model_System_Config_Backend_Admin_Custom::CONFIG_SCOPE,
                Mage_Adminhtml_Model_System_Config_Backend_Admin_Custom::CONFIG_SCOPE_ID,
            );
            Mage::getConfig()->deleteConfig(
                Mage_Adminhtml_Model_System_Config_Backend_Admin_Custom::XML_PATH_UNSECURE_BASE_URL,
                Mage_Adminhtml_Model_System_Config_Backend_Admin_Custom::CONFIG_SCOPE,
                Mage_Adminhtml_Model_System_Config_Backend_Admin_Custom::CONFIG_SCOPE_ID,
            );
        }

        // Set redirect flag if use custom admin URL changed
        if ($this->getOldValue() != $value) {
            Mage::register('custom_admin_path_redirect', true, true);
        }

        return $this;
    }
}
