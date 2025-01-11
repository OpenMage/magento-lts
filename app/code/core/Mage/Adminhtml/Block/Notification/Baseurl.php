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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Notification_Baseurl extends Mage_Adminhtml_Block_Template
{
    /**
     * Get url for config settings where base url option can be changed
     *
     * @return string|false
     */
    public function getConfigUrl()
    {
        $defaultUnsecure = (string) Mage::getConfig()->getNode('default/' . Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL);
        $defaultSecure  = (string) Mage::getConfig()->getNode('default/' . Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL);

        if ($defaultSecure === '{{base_url}}' || $defaultUnsecure === '{{base_url}}') {
            return $this->getUrl('adminhtml/system_config/edit', ['section' => 'web']);
        }

        $configData = Mage::getModel('core/config_data');
        $dataCollection = $configData->getCollection()
            ->addValueFilter('{{base_url}}');

        $url = false;
        foreach ($dataCollection as $data) {
            if ($data->getScope() === 'stores') {
                $code = Mage::app()->getStore($data->getScopeId())->getCode();
                $url = $this->getUrl('adminhtml/system_config/edit', ['section' => 'web', 'store' => $code]);
            }
            if ($data->getScope() === 'websites') {
                $code = Mage::app()->getWebsite($data->getScopeId())->getCode();
                $url = $this->getUrl('adminhtml/system_config/edit', ['section' => 'web', 'website' => $code]);
            }

            if ($url) {
                return $url;
            }
        }
        return $url;
    }
}
