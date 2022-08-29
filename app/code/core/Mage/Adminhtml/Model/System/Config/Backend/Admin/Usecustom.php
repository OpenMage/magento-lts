<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml backend model for "Use Custom Admin URL" option
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
                Mage_Adminhtml_Model_System_Config_Backend_Admin_Custom::CONFIG_SCOPE_ID
            );
            Mage::getConfig()->deleteConfig(
                Mage_Adminhtml_Model_System_Config_Backend_Admin_Custom::XML_PATH_UNSECURE_BASE_URL,
                Mage_Adminhtml_Model_System_Config_Backend_Admin_Custom::CONFIG_SCOPE,
                Mage_Adminhtml_Model_System_Config_Backend_Admin_Custom::CONFIG_SCOPE_ID
            );
        }

        return $this;
    }
}
