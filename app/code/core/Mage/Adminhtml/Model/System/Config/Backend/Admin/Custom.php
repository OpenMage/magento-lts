<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml backend model for "Use Secure URLs in Admin" option
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_System_Config_Backend_Admin_Custom extends Mage_Core_Model_Config_Data
{
    const CONFIG_SCOPE                      = 'default';
    const CONFIG_SCOPE_ID                   = 0;

    const XML_PATH_UNSECURE_BASE_LINK_URL        = 'web/unsecure/base_link_url';
    const XML_PATH_SECURE_BASE_LINK_URL          = 'web/secure/base_link_url';

    /**
     * Check whether redirect should be set
     *
     * @return Mage_Adminhtml_Model_System_Config_Backend_Admin_Custom
     */
    protected function _beforeSave()
    {
        if ($this->getOldValue() != $this->getValue()) {
            Mage::register('custom_admin_url_redirect', true, true);
        }
        return $this;
    }
}
